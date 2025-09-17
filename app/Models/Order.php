<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\Auditable;

class Order extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'user_id','address_id','delivery_slot_id','status','payment_mode','payment_status','currency',
        'subtotal','tax','discount','shipping_fee','total','coupon_code','razorpay_order_id','notes'
    ];
    protected $casts = [
        'subtotal'=>'decimal:2','tax'=>'decimal:2','discount'=>'decimal:2','shipping_fee'=>'decimal:2','total'=>'decimal:2',
        'notes'=>'array'
    ];

    // Cache for order counts per day to avoid repeated queries
    protected static $dayOrderCounts = [];

    // Get dynamic order ID based on created date and daily sequence
    public function getOrderIdAttribute()
    {
        if (!$this->created_at) {
            return $this->id;
        }

        // Get the date prefix from created_at
        $datePrefix = $this->created_at->format('Ymd');
        $dateKey = $this->created_at->toDateString();

        // Check if we already calculated orders for this day
        if (!isset(static::$dayOrderCounts[$dateKey])) {
            // Get all orders for this day and store their sequence
            $dayOrders = static::whereDate('created_at', $dateKey)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            static::$dayOrderCounts[$dateKey] = array_flip($dayOrders);
        }

        // Get this order's position in the day (0-indexed)
        $position = static::$dayOrderCounts[$dateKey][$this->id] ?? 0;
        $sequentialNumber = $position + 1;

        // Format: 2025091501, 2025091502, etc.
        if ($sequentialNumber <= 99) {
            return $datePrefix . str_pad($sequentialNumber, 2, '0', STR_PAD_LEFT);
        } else {
            return $datePrefix . $sequentialNumber;
        }
    }

    // Get formatted order ID for display (with dashes)
    public function getFormattedOrderIdAttribute()
    {
        $orderId = $this->order_id;

        if (strlen($orderId) >= 10) {
            $year = substr($orderId, 0, 4);
            $month = substr($orderId, 4, 2);
            $day = substr($orderId, 6, 2);
            $serial = substr($orderId, 8);
            return "{$year}-{$month}-{$day}-{$serial}";
        }

        return $orderId;
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function address(){ return $this->belongsTo(Address::class); }
    public function slot(){ return $this->belongsTo(DeliverySlot::class,'delivery_slot_id'); }
    public function items(){ return $this->hasMany(OrderItem::class); }
    public function payments(){ return $this->hasMany(Payment::class); }
    public function invoice(){ return $this->hasOne(Invoice::class); }
    public function shipment(){ return $this->hasOne(Shipment::class); }

    // Business rules
    public function isCancelableByUser(): bool
    {
        try {
            if (!in_array($this->status, ['placed','processing'], true)) return false;
            $notes = $this->notes;
            if (is_string($notes) && $notes !== '') {
                $notes = json_decode($notes, true);
            }
            if (!is_array($notes)) $notes = [];
            $date = $notes['delivery_date'] ?? null;
            if (!$date) return false;
            $delivery = \Carbon\Carbon::parse($date.' '.($notes['delivery_time'] ?? '00:00'));
            // Only allow cancel strictly before one full day of delivery
            return now()->lt($delivery->copy()->startOfDay()->subDay());
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function cancel(string $by = 'system', ?string $reason = null): void
    {
        if ($this->status === 'cancelled') return;
        
        $oldStatus = $this->status;
        $this->status = 'cancelled';
        $rawNotes = $this->notes;
        
        // Normalize notes to an array even if stored as JSON string
        if (is_array($rawNotes)) {
            $n = $rawNotes;
        } elseif (is_string($rawNotes) && $rawNotes !== '') {
            $decoded = json_decode($rawNotes, true);
            $n = is_array($decoded) ? $decoded : [];
        } else {
            $n = [];
        }
        $n['cancelled_by'] = $by;
        $n['cancelled_at'] = now()->toDateTimeString();
        $n['previous_status'] = $oldStatus;
        if ($reason) $n['cancel_reason'] = $reason;
        $this->notes = $n;
        $this->save();

        // Restock products - only if order was not already cancelled or delivered
        if (in_array($oldStatus, ['placed', 'processing', 'confirmed'])) {
            $this->restockItems();
        }
    }
    
    public function restockItems(): void
    {
        foreach ($this->items as $item) {
            try {
                $product = $item->product;
                if ($product) {
                    // Increment stock
                    $product->increment('stock', (int) $item->qty);
                    
                    // Update total_stock if it exists
                    if ($product->hasAttribute('total_stock')) {
                        $product->increment('total_stock', (int) $item->qty);
                    }
                    
                    Log::info("Restocked {$item->qty} units for product {$product->name} (ID: {$product->id})");
                }
                
                // Handle product variants if applicable
                if ($item->product_variant_id) {
                    \App\Models\ProductVariant::where('id', $item->product_variant_id)
                        ->increment('stock', (int) $item->qty);
                }
            } catch (\Throwable $e) {
                Log::error("Failed to restock item {$item->id}: " . $e->getMessage());
            }
        }
    }
    
    public function decrementStock(): void
    {
        foreach ($this->items as $item) {
            try {
                $product = $item->product;
                if ($product && $product->stock >= $item->qty) {
                    // Decrement stock
                    $product->decrement('stock', (int) $item->qty);
                    
                    // Update total_stock if it exists
                    if ($product->hasAttribute('total_stock') && $product->total_stock >= $item->qty) {
                        $product->decrement('total_stock', (int) $item->qty);
                    }
                    
                    Log::info("Decremented {$item->qty} units for product {$product->name} (ID: {$product->id})");
                } else {
                    $productName = $product?->name ?? $item->name_snapshot ?? 'Unknown Product';
                    $productId = $product?->id ?? 'Unknown ID';
                    Log::warning("Insufficient stock for product {$productName} (ID: {$productId})");
                }
                
                // Handle product variants if applicable
                if ($item->product_variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                    if ($variant && $variant->stock >= $item->qty) {
                        $variant->decrement('stock', (int) $item->qty);
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Failed to decrement stock for item {$item->id}: " . $e->getMessage());
            }
        }
    }
}
