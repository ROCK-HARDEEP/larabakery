<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LimitedTimeOffer extends Model
{
    use HasFactory;

    protected $table = 'limited_time_offers';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_rule_json',
        'image_path',
        'price',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'is_active',
        'product_ids', // JSON array of product IDs included in the offer
        'max_quantity', // Maximum quantity available
        'sold_quantity', // Track how many sold
    ];

    protected $casts = [
        'price_rule_json' => 'array',
        'product_ids' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_quantity' => 'integer',
        'sold_quantity' => 'integer',
    ];

    /**
     * Get the products included in this offer
     */
    public function products()
    {
        if (empty($this->product_ids)) {
            return Product::query()->whereRaw('0 = 1');
        }
        
        return Product::whereIn('id', $this->product_ids);
    }

    /**
     * Check if the offer is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->starts_at && $now < $this->starts_at) {
            return false;
        }
        
        if ($this->ends_at && $now > $this->ends_at) {
            return false;
        }

        // Check if sold out
        if ($this->max_quantity && $this->sold_quantity >= $this->max_quantity) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the time remaining for the offer
     */
    public function getTimeRemaining(): ?string
    {
        if (!$this->ends_at) {
            return null;
        }

        if ($this->ends_at->isPast()) {
            return 'Expired';
        }

        $diff = now()->diff($this->ends_at);
        
        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes';
        } else {
            return $diff->i . ' minutes';
        }
    }

    /**
     * Get the discount amount
     */
    public function getDiscountAmount(): float
    {
        if ($this->discount_percentage) {
            return $this->price * ($this->discount_percentage / 100);
        }
        return 0;
    }

    /**
     * Get the discounted price
     */
    public function getDiscountedPrice(): float
    {
        return $this->price - $this->getDiscountAmount();
    }

    /**
     * Get remaining quantity
     */
    public function getRemainingQuantity(): ?int
    {
        if (!$this->max_quantity) {
            return null; // Unlimited
        }
        
        return max(0, $this->max_quantity - $this->sold_quantity);
    }

    /**
     * Check if offer is sold out
     */
    public function isSoldOut(): bool
    {
        if (!$this->max_quantity) {
            return false;
        }
        
        return $this->sold_quantity >= $this->max_quantity;
    }

    /**
     * Increment sold quantity
     */
    public function recordSale(int $quantity = 1): void
    {
        $this->increment('sold_quantity', $quantity);
    }

    /**
     * Scope for active offers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('starts_at')
                           ->orWhere('starts_at', '<=', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('ends_at')
                           ->orWhere('ends_at', '>', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('max_quantity')
                           ->orWhereColumn('sold_quantity', '<', 'max_quantity');
                     });
    }

    /**
     * Scope for upcoming offers
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
                     ->where('starts_at', '>', now());
    }

    /**
     * Scope for expired offers
     */
    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }
}