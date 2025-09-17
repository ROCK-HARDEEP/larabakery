<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Bundle extends Model
{
    use HasFactory;
    
    protected $table = 'limited_time_offers';
    
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'image_path',
        'price',
        'price_rule_json',
        'product_ids',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'max_quantity',
        'sold_quantity',
        'is_active'
    ];
    
    protected $casts = [
        'price_rule_json' => 'array',
        'product_ids' => 'array',
        'is_active' => 'bool',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_quantity' => 'integer',
        'sold_quantity' => 'integer',
    ];
    
    public function items()
    {
        return $this->hasMany(BundleItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, BundleItem::class, 'bundle_id', 'product_id')
            ->withPivot('qty');
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        return true;
    }
    
    public function getOriginalPriceAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->product) {
                $total += $item->product->price * $item->qty;
            }
        }
        return $total;
    }
    
    public function getSavingsAttribute()
    {
        return $this->original_price - $this->price;
    }
    
    public function getSavingsPercentageAttribute()
    {
        if ($this->original_price > 0) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }
}