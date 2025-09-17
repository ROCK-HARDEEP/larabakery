<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Traits\Auditable;

class Coupon extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'code',
        'type',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'product_ids',
        'expires_at',
        'usage_limit',
        'usage_count',
        'usage_limit_per_customer',
        'is_active',
        'description',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'usage_limit_per_customer' => 'integer',
    ];

    /**
     * Get the products associated with this coupon
     */
    public function products()
    {
        if ($this->type !== 'product_specific' || empty($this->product_ids)) {
            return Product::query()->whereRaw('0 = 1'); // Return empty query
        }
        
        return Product::whereIn('id', $this->product_ids);
    }

    /**
     * Get coupon usage records
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check expiry
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon is valid for a specific user
     */
    public function isValidForUser($user, $orderAmount = 0, $products = []): array
    {
        // Basic validation
        if (!$this->isValid()) {
            return ['valid' => false, 'message' => 'This coupon code is invalid or expired'];
        }

        // Check type-specific conditions
        switch ($this->type) {
            case 'first_time_user':
                if ($user) {
                    $hasOrders = Order::where('user_id', $user->id)->exists();
                    if ($hasOrders) {
                        return ['valid' => false, 'message' => 'This coupon is only for first-time users'];
                    }
                }
                break;

            case 'order_above':
                if ($orderAmount < $this->minimum_order_amount) {
                    return [
                        'valid' => false, 
                        'message' => 'Minimum order amount of ₹' . number_format($this->minimum_order_amount, 2) . ' required'
                    ];
                }
                break;

            case 'product_specific':
                if (empty($products)) {
                    return ['valid' => false, 'message' => 'No eligible products in cart for this coupon'];
                }
                
                $productIds = is_array($products) ? $products : collect($products)->pluck('id')->toArray();
                $eligibleProducts = array_intersect($productIds, $this->product_ids ?? []);
                
                if (empty($eligibleProducts)) {
                    return ['valid' => false, 'message' => 'This coupon is not valid for the products in your cart'];
                }
                break;
        }

        // Check per-customer usage limit
        if ($this->usage_limit_per_customer && $user) {
            $userUsageCount = $this->usages()
                ->where('user_id', $user->id)
                ->count();
                
            if ($userUsageCount >= $this->usage_limit_per_customer) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully'];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($orderAmount, $products = []): float
    {
        $baseAmount = $orderAmount;
        
        // For product-specific coupons, calculate based on eligible products only
        if ($this->type === 'product_specific' && !empty($products)) {
            $eligibleAmount = 0;
            foreach ($products as $product) {
                if (in_array($product['id'] ?? $product->id, $this->product_ids ?? [])) {
                    $eligibleAmount += ($product['price'] ?? $product->price) * ($product['quantity'] ?? 1);
                }
            }
            $baseAmount = $eligibleAmount;
        }

        // Calculate discount
        if ($this->discount_type === 'percentage') {
            $discount = $baseAmount * ($this->discount_value / 100);
        } else {
            $discount = min($this->discount_value, $baseAmount);
        }

        return round($discount, 2);
    }

    /**
     * Record usage of this coupon
     */
    public function recordUsage($user = null, $orderId = null, $discountAmount = 0, $email = null)
    {
        // Create usage record
        $this->usages()->create([
            'user_id' => $user ? $user->id : null,
            'order_id' => $orderId,
            'customer_email' => $email ?? ($user ? $user->email : null),
            'discount_amount' => $discountAmount,
        ]);

        // Increment usage count
        $this->increment('usage_count');
    }

    /**
     * Get display label for type
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'first_time_user' => 'First Time User',
            'order_above' => 'Minimum Order Amount',
            'product_specific' => 'Product Specific',
            default => $this->type,
        };
    }

    /**
     * Get display label for discount
     */
    public function getDiscountLabel(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '% OFF';
        } else {
            return '₹' . number_format($this->discount_value, 2) . ' OFF';
        }
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUses(): ?int
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }
        
        return max(0, $this->usage_limit - $this->usage_count);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('usage_limit')
                           ->orWhereColumn('usage_count', '<', 'usage_limit');
                     });
    }

    /**
     * Generate a unique coupon code
     */
    public static function generateCode($prefix = '', $length = 8): string
    {
        do {
            $code = strtoupper($prefix . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}