<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_position',
        'customer_company',
        'review',
        'rating',
        'customer_image',
        'is_featured',
        'is_active',
        'location',
        'review_date',
        'product_reviewed',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
        'review_date' => 'date',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    public function scopeByRating($query, $rating = 5)
    {
        return $query->where('rating', '>=', $rating);
    }

    // Accessors
    public function getStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '⭐';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    public function getFormattedReviewDateAttribute()
    {
        return $this->review_date ? $this->review_date->format('M d, Y') : $this->created_at->format('M d, Y');
    }

    public function getCustomerFullNameAttribute()
    {
        $name = $this->customer_name;
        
        if ($this->customer_position && $this->customer_company) {
            $name .= ', ' . $this->customer_position . ' at ' . $this->customer_company;
        } elseif ($this->customer_position) {
            $name .= ', ' . $this->customer_position;
        } elseif ($this->customer_company) {
            $name .= ', ' . $this->customer_company;
        }
        
        return $name;
    }

    public function getShortReviewAttribute()
    {
        return \Illuminate\Support\Str::limit($this->review, 150);
    }
}