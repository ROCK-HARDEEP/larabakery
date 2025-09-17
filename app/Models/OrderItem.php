<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id','product_id','product_variant_id','name_snapshot','sku_snapshot','price','qty','addons_json','line_subtotal','line_tax','line_total'
    ];
    protected $casts = [
        'price'=>'decimal:2','qty'=>'int','addons_json'=>'array','line_subtotal'=>'decimal:2','line_tax'=>'decimal:2','line_total'=>'decimal:2'
    ];
    public function order(){ return $this->belongsTo(Order::class); }
    public function product(){ return $this->belongsTo(Product::class); }
    public function variant(){ return $this->belongsTo(ProductVariant::class,'product_variant_id'); }
    
    // Accessor to get name with fallback
    public function getNameAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        // Fallback to name_snapshot
        if (!empty($this->name_snapshot)) {
            return $this->name_snapshot;
        }
        
        // Fallback to product name
        if ($this->product) {
            return $this->product->name;
        }
        
        return 'Product';
    }
    
    // Calculate discount amount
    public function getDiscountAmountAttribute()
    {
        $originalPrice = $this->price * $this->qty;
        return max(0, $originalPrice - $this->line_total);
    }
}