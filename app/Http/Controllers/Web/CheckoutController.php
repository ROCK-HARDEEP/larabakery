<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use App\Services\PincodeService;
use App\Services\PricingService;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private PricingService $pricing,
        private PincodeService $pincodes,
        private SlotService $slots,
    ){}

    public function summary()
    {
        $items = $this->cart->items();
        $summary = $this->pricing->summarize($items, session('coupon'), 5.0);
        return view('web.checkout.summary', compact('items','summary'));
    }

    public function summaryNext(Request $request)
    {
        $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today',
            'slot' => 'nullable|string',
            'custom_time' => 'nullable|date_format:H:i',
        ]);

        $time = null;
        $preset = $request->input('slot');
        $custom = $request->input('custom_time');

        if ($custom) {
            // validate custom within 10:00 - 18:00
            if ($custom < '10:00' || $custom > '18:00') {
                return back()->withErrors(['custom_time' => 'Please choose a time between 10:00 and 18:00']);
            }
            $time = $custom;
        } elseif (in_array($preset, ['10:00','14:00','17:00'], true)) {
            $time = $preset;
        } else {
            return back()->withErrors(['slot' => 'Please select a delivery time']);
        }

        session([
            'checkout.delivery_date' => $request->delivery_date,
            'checkout.delivery_time' => $time,
        ]);
        return redirect()->route('checkout.address');
    }

    public function address()
    {
        // guard: require summary step
        if (!session()->has('checkout.delivery_time')) {
            return redirect()->route('checkout.summary');
        }
        return view('web.checkout.address');
    }

    public function addressNext(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'line1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
        ]);
        // Check serviceability before proceeding
        if (!$this->pincodes->isServiceable($request->pincode)) {
            return back()->withErrors(['pincode' => 'Delivery not available for this pincode'])->withInput();
        }
        // Persist phone to user profile if logged in and model supports it
        try {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user instanceof User) {
                    $usersTable = (new User())->getTable();
                    if (Schema::hasColumn($usersTable, 'phone')) {
                        $user->phone = $request->phone;
                        $user->save();
                    }
                }
            }
        } catch (\Throwable $e) {}
        session(['checkout.address' => $request->only('name','phone','line1','line2','city','state','pincode')]);
        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        // guard: require address step
        if (!session()->has('checkout.address')) {
            return redirect()->route('checkout.address');
        }
        $items = $this->cart->items();
        $summary = $this->pricing->summarize($items, session('coupon'), 5.0);
        return view('web.checkout.payment', compact('items','summary'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code'=>'required|string']);
        session(['coupon' => strtoupper(trim($request->code))]);
        return back();
    }

    public function validatePincode(Request $request)
    {
        $request->validate(['pincode'=>'required|string']);
        return response()->json(['ok' => $this->pincodes->isServiceable($request->pincode)]);
    }

    public function slots(Request $request)
    {
        $request->validate(['date'=>'required|date']);
        return response()->json(['slots' => $this->slots->forDate($request->date)]);
    }

    public function placeOrder(Request $request)
    {
        // guard: require previous steps
        if (!session()->has('checkout.delivery_time')) {
            return redirect()->route('checkout.summary');
        }
        if (!session()->has('checkout.address')) {
            return redirect()->route('checkout.address');
        }
        $request->validate([
            'payment_mode' => 'required|in:cod,razorpay',
        ]);

        $addr = session('checkout.address');
        $pin = $addr['pincode'] ?? $request->pincode ?? null;
        if (!$pin) {
            return redirect()->route('checkout.address')->withErrors(['pincode' => 'Please enter a valid pincode']);
        }
        if (!$this->pincodes->isServiceable($pin)) {
            return redirect()->route('checkout.address')->withErrors(['pincode' => 'Delivery not available to this pincode']);
        }

        $items = $this->cart->items();
        if (empty($items)) return back()->withErrors(['cart'=>'Cart is empty']);

        $summary = $this->pricing->summarize($items, session('coupon'), 5.0);

        return DB::transaction(function() use ($request, $items, $summary) {
            $userId = Auth::id();

            $addr = session('checkout.address');
            $address = Address::create([
                'user_id' => $userId,
                'label' => 'Shipping',
                'line1' => $addr['line1'] ?? $request->line1,
                'line2' => $addr['line2'] ?? $request->line2,
                'pincode' => $addr['pincode'] ?? $request->pincode,
                'city' => $addr['city'] ?? $request->city,
                'state_iso' => $request->state ?? 'IN-AP',
                'country' => 'IN',
                'is_default' => false,
            ]);

            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $address->id,
                'delivery_slot_id' => null,
                'status' => 'placed',
                'payment_mode' => $request->payment_mode ?? 'cod',
                'payment_status' => ($request->payment_mode ?? 'cod') === 'cod' ? 'pending' : 'paid',
                'currency' => 'INR',
                'subtotal' => $summary['subtotal'],
                'tax' => $summary['tax'],
                'discount' => $summary['discount'],
                'shipping_fee' => $summary['shipping'],
                'total' => $summary['total'],
                'coupon_code' => session('coupon'),
                'notes' => json_encode([
                    'delivery_date' => session('checkout.delivery_date'),
                    'delivery_time' => session('checkout.delivery_time'),
                ]),
            ]);

            foreach ($items as $key => $it) {
                $variantId = $it['variant_id'] ?? null;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'product_variant_id' => $variantId ?: null,
                    'name_snapshot' => $it['name'],
                    'sku_snapshot' => null,
                    'price' => $it['price'],
                    'qty' => $it['qty'],
                    'addons_json' => $it['addons'],
                    'line_subtotal' => $it['price'] * $it['qty'],
                    'line_tax' => 0,
                    'line_total' => $it['price'] * $it['qty'],
                ]);

                // Decrement stock on variant
                if ($variantId) {
                    ProductVariant::where('id', $variantId)
                        ->where('stock_quantity', '>=', (int) $it['qty'])
                        ->decrement('stock_quantity', (int) $it['qty']);
                } else {
                    // If no variant, get default variant and decrement its stock
                    $defaultVariant = ProductVariant::where('product_id', $it['product_id'])
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->first();

                    if ($defaultVariant) {
                        $defaultVariant->where('stock_quantity', '>=', (int) $it['qty'])
                            ->decrement('stock_quantity', (int) $it['qty']);
                    }
                }
            }

            // Clear cart after placing order
            $this->cart->clear();

            // Clear step data
            session()->forget(['checkout.delivery_date','checkout.delivery_time','checkout.address']);

            return redirect()->route('checkout.success', $order->id);
        });
    }

    public function success(Order $order)
    {
        return view('web.checkout.success', compact('order'));
    }
}
