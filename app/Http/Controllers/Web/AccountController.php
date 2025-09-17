<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class AccountController extends Controller
{
    public function index(){ return redirect()->route('account.profile'); }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('web.account.orders', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items','payments','invoice');
        return view('web.account.order_show', compact('order'));
    }

    public function cancelOrder(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        if (!$order->isCancelableByUser()) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }
        $order->load('items');
        $order->cancel('user');
        // notify admin(s)
        try {
            Notification::route('database', 'admin')->notify(new \App\Notifications\UserCancelledOrder($order));
        } catch (\Throwable $e) {}
        return back()->with('success', 'Order cancelled successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        $userId = $user?->id;
        $addresses = Address::where('user_id', $userId)->get();
        return view('web.account.profile', compact('user','addresses'));
    }

    public function profileUpdate(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'gstin' => 'nullable|string|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['phone'])) { try { $user->phone = $validated['phone']; } catch (\Throwable $e) {} }
        if (!empty($validated['username'])) { $user->username = $validated['username']; }
        if (!empty($validated['gstin'])) { $user->gstin = $validated['gstin']; }
        if (!empty($validated['password'])) { $user->password = Hash::make($validated['password']); }
        $user->save();
        return back()->with('success','Profile updated');
    }

    public function addressStore(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
        ]);
        $data['user_id'] = Auth::id();
        Address::create($data);
        return back()->with('success','Address added');
    }

    public function addressDelete(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->delete();
        return back()->with('success','Address removed');
    }
}
