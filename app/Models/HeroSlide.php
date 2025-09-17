<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'title','subtitle','image_path','button_label','category_id','product_id','title_color','subtitle_color','title_size','subtitle_size','is_active','sort_order'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
            'title_size' => 'integer',
            'subtitle_size' => 'integer',
        ];
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function product()  { return $this->belongsTo(Product::class); }

    /**
     * Check if the button should be displayed
     */
    public function shouldShowButton(): bool
    {
        return !empty($this->button_label);
    }

    /**
     * Get the button URL based on category or product
     */
    public function getButtonUrl(): ?string
    {
        if ($this->category_id && $this->category) {
            return route('category.show', $this->category->slug);
        }
        
        if ($this->product_id && $this->product) {
            return route('product.show', $this->product->slug);
        }
        
        return null;
    }

    /**
     * Get the button target (category or product)
     */
    public function getButtonTarget(): ?string
    {
        if ($this->category_id && $this->category) {
            return 'category';
        }
        
        if ($this->product_id && $this->product) {
            return 'product';
        }
        
        return null;
    }

    /**
     * Get the button target name
     */
    public function getButtonTargetName(): ?string
    {
        if ($this->category_id && $this->category) {
            return $this->category->name;
        }
        
        if ($this->product_id && $this->product) {
            return $this->product->name;
        }
        
        return null;
    }

    /**
     * Check if the slide has a valid target
     */
    public function hasValidTarget(): bool
    {
        return ($this->category_id && $this->category) || ($this->product_id && $this->product);
    }

    /**
     * Get the full URL for the hero slide image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return url('storage/' . $this->image_path);
        }
        return url('img/placeholder-hero.jpg');
    }

    /**
     * Get thumbnail URL for admin display
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }
}


