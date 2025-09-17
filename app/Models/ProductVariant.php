<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'sku',
        'variant_type',
        'variant_value',
        'price',
        'compare_at_price',
        'stock_quantity',
        'stock',
        'weight',
        'dimensions',
        'image',
        'attributes_json',
        'is_active',
        'sort_order'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock' => 'int',
        'stock_quantity' => 'integer',
        'attributes_json' => 'array',
        'is_active' => 'bool',
        'sort_order' => 'integer'
    ];
    
    public function product()
    { 
        return $this->belongsTo(Product::class); 
    }
    
    public function getDisplayNameAttribute()
    {
        return $this->product->name . ' - ' . ($this->variant_value ?? $this->sku);
    }
    
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }
    
    public function isInStock()
    {
        return ($this->stock_quantity ?? $this->stock ?? 0) > 0;
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('stock_quantity', '>', 0)
              ->orWhere('stock', '>', 0);
        });
    }
}
