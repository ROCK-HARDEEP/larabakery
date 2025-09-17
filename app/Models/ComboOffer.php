<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComboOffer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'original_price',
        'combo_price',
        'discount_percentage',
        'max_quantity',
        'sold_quantity',
        'is_active',
        'display_order',
        'starts_at',
        'ends_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'original_price' => 'decimal:2',
        'combo_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'max_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'display_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            
            // Calculate discount percentage if not set
            if ($model->original_price > 0 && $model->combo_price > 0 && !$model->discount_percentage) {
                $model->discount_percentage = round((($model->original_price - $model->combo_price) / $model->original_price) * 100, 2);
            }
        });
        
        static::updating(function ($model) {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
            
            // Recalculate discount percentage
            if (($model->isDirty('original_price') || $model->isDirty('combo_price')) && $model->original_price > 0) {
                $model->discount_percentage = round((($model->original_price - $model->combo_price) / $model->original_price) * 100, 2);
            }
        });
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_offer_products')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }
    
    public function getSavingsAttribute()
    {
        return $this->original_price - $this->combo_price;
    }
    
    public function getSavingsPercentageAttribute()
    {
        if ($this->original_price > 0) {
            return round((($this->original_price - $this->combo_price) / $this->original_price) * 100);
        }
        return 0;
    }
    
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        
        return true;
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }
    
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }
}