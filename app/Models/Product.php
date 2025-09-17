<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'category_id','name','slug','hsn_code','tax_rate','rating','review_count','taste_type','taste_value','taste_level','taste_description','description','full_description','ingredients','nutritional_info','allergen_info','storage_instructions','shelf_life','images_path','is_active','meta'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'tax_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'meta' => 'array',
        'nutritional_info' => 'array',
        'images_path' => 'array',
    ];

    public function category(){ return $this->belongsTo(Category::class); }
    public function attributes(){ return $this->hasMany(ProductAttribute::class); }
    public function variants(){ return $this->hasMany(ProductVariant::class); }
    public function addons(){ return $this->hasMany(ProductAddon::class); }
    public function orderItems(){ return $this->hasMany(OrderItem::class); }
    public function faqs(){ return $this->hasMany(ProductFaq::class); }

    // Get minimum price from variants
    public function getMinPriceAttribute()
    {
        return $this->variants()->where('is_active', true)->min('price') ?? 0;
    }

    // Get maximum price from variants
    public function getMaxPriceAttribute()
    {
        return $this->variants()->where('is_active', true)->max('price') ?? 0;
    }

    // Get price range string for display
    public function getPriceRangeAttribute()
    {
        $min = $this->min_price;
        $max = $this->max_price;

        if ($min == $max) {
            return '₹' . number_format($min, 2);
        }

        return '₹' . number_format($min, 2) . ' - ₹' . number_format($max, 2);
    }

    // Get display price (minimum price with "From" prefix if multiple variants)
    public function getDisplayPriceAttribute()
    {
        $min = $this->min_price;
        $max = $this->max_price;

        if ($min == $max || $max == 0) {
            return '₹' . number_format($min, 2);
        }

        return 'From ₹' . number_format($min, 2);
    }

    // Accessor for image (first image from images_path array)
    public function getImageAttribute()
    {
        if (!empty($this->images_path) && is_array($this->images_path)) {
            return $this->images_path[0];
        }
        return null;
    }

    // Enhanced search scope with multiple search fields and relevance scoring
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $searchTerms = explode(' ', trim($search));
        
        return $query->where(function($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $term = trim($term);
                if (strlen($term) >= 2) {
                    $q->where(function($subQ) use ($term) {
                        $subQ->where('name', 'LIKE', '%' . $term . '%')
                              ->orWhere('description', 'LIKE', '%' . $term . '%')
                              ->orWhere('slug', 'LIKE', '%' . $term . '%')
                              ->orWhereHas('category', function($catQ) use ($term) {
                                  $catQ->where('name', 'LIKE', '%' . $term . '%');
                              });
                    });
                }
            }
        });
    }

    // Advanced search with filters and sorting
    public function scopeAdvancedSearch($query, $search, $filters = [])
    {
        // Basic search
        if (!empty($search)) {
            $query->search($search);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Price range filter using variants
        if (!empty($filters['min_price'])) {
            $query->whereHas('variants', function($q) use ($filters) {
                $q->where('price', '>=', (float) $filters['min_price'])
                  ->where('is_active', true);
            });
        }
        if (!empty($filters['max_price'])) {
            $query->whereHas('variants', function($q) use ($filters) {
                $q->where('price', '<=', (float) $filters['max_price'])
                  ->where('is_active', true);
            });
        }

        // Stock filter using variants
        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0)
                  ->where('is_active', true);
            });
        }

        // Active products only
        $query->where('is_active', true);

        return $query;
    }

    // Search by category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Search by price range using variants
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereHas('variants', function($q) use ($min, $max) {
            if ($min !== null) {
                $q->where('price', '>=', (float) $min);
            }
            if ($max !== null) {
                $q->where('price', '<=', (float) $max);
            }
            $q->where('is_active', true);
        });
    }

    // Search by availability using variants
    public function scopeInStock($query)
    {
        return $query->whereHas('variants', function($q) {
            $q->where('stock_quantity', '>', 0)
              ->where('is_active', true);
        });
    }

    // Search by name similarity (fuzzy search)
    public function scopeFuzzySearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $searchTerms = explode(' ', trim($search));
        
        return $query->where(function($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $term = trim($term);
                if (strlen($term) >= 2) {
                    $q->where(function($subQ) use ($term) {
                        $subQ->where('name', 'LIKE', '%' . $term . '%')
                              ->orWhere('description', 'LIKE', '%' . $term . '%')
                              ->orWhere('slug', 'LIKE', '%' . $term . '%')
                              ->orWhereHas('category', function($catQ) use ($term) {
                                  $catQ->where('name', 'LIKE', '%' . $term . '%');
                              });
                    });
                }
            }
        });
    }

    // Image accessor methods bound to the correct column: images_path
    public function getImagesPathAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function getFirstImageAttribute()
    {
        $images = $this->images_path;
        if (is_array($images) && !empty($images)) {
            return $images[0];
        }
        return null;
    }

    public function getImageUrlAttribute()
    {
        $firstImage = $this->first_image;
        if ($firstImage) {
            return url('storage/' . $firstImage);
        }
        return url('img/placeholder-product.jpg');
    }

    // Get all image URLs
    public function getImageUrlsAttribute()
    {
        $images = $this->images_path ?? [];
        if (empty($images)) {
            return [];
        }
        
        return array_map(function($image) {
            return asset('storage/' . $image);
        }, $images);
    }

    // Get thumbnail URL for admin display
    public function getThumbnailUrlAttribute()
    {
        $firstImage = $this->first_image;
        if ($firstImage) {
            return asset('storage/' . $firstImage);
        }
        return null;
    }

    // Compute available stock from variants
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants()->where('is_active', true)->sum('stock_quantity');
    }

    // Check if product has multiple variants with different prices
    public function getHasVariablePricingAttribute()
    {
        return $this->min_price != $this->max_price;
    }

    // Get all active variants
    public function getActiveVariantsAttribute()
    {
        return $this->variants()->where('is_active', true)->orderBy('sort_order')->get();
    }

    // Dynamic tagging system
    public function getIsNewAttribute()
    {
        // Get the 5 most recently created products
        $recentProducts = static::orderBy('created_at', 'desc')->take(5)->pluck('id');
        return $recentProducts->contains($this->id);
    }

    public function getIsPopularAttribute()
    {
        // Check if product has high order count (you can adjust the threshold)
        $orderCount = $this->orderItems()->count();
        return $orderCount >= 10; // Show as popular if ordered 10+ times
    }

    public function getOrderCountAttribute()
    {
        return $this->orderItems()->count();
    }

    // Get dynamic tag for display
    public function getDynamicTagAttribute()
    {
        if ($this->is_new) {
            return 'new';
        }

        if ($this->is_popular) {
            return 'popular';
        }

        return null;
    }

    public function getDynamicTagLabelAttribute()
    {
        switch ($this->dynamic_tag) {
            case 'new':
                return 'New';
            case 'popular':
                return 'Popular';
            default:
                return null;
        }
    }

    // Scope for new products
    public function scopeNew($query)
    {
        $recentProductIds = static::orderBy('created_at', 'desc')->take(5)->pluck('id');
        return $query->whereIn('id', $recentProductIds);
    }

    // Scope for popular products (based on orders)
    public function scopePopular($query)
    {
        return $query->withCount('orderItems')
                    ->having('order_items_count', '>=', 10)
                    ->orderBy('order_items_count', 'desc');
    }

    // Review methods
    public function addReview(float $rating): void
    {
        $currentTotal = $this->rating * $this->review_count;
        $newTotal = $currentTotal + $rating;
        $newCount = $this->review_count + 1;
        $newRating = $newTotal / $newCount;
        
        $this->update([
            'rating' => round($newRating, 2),
            'review_count' => $newCount
        ]);
    }

    public function getFormattedRatingAttribute(): string
    {
        $rating = $this->rating ?? 0;
        return number_format((float)$rating, 1);
    }

    public function getStarsAttribute(): array
    {
        $rating = $this->rating ?? 0;
        $stars = [];

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars[] = 'full';
            } elseif ($i - $rating < 1) {
                $stars[] = 'partial';
            } else {
                $stars[] = 'empty';
            }
        }

        return $stars;
    }

    // Get display rating
    public function getDisplayRatingAttribute()
    {
        return $this->rating ?? 0;
    }

    // Get display review count
    public function getDisplayReviewCountAttribute()
    {
        return $this->review_count ?? 0;
    }

    // Accessor for reviews_count (used in views)
    public function getReviewsCountAttribute()
    {
        return $this->display_review_count;
    }

    // Taste attribute accessors
    public function getTasteDisplayAttribute()
    {
        if (!$this->taste_type) {
            return null;
        }

        $typeLabel = ucfirst($this->taste_type);

        // If taste_value is set, display "Sweetness: honey sweetness" format
        if ($this->taste_value) {
            return "{$typeLabel}: {$this->taste_value}";
        }

        // Fallback to old level format if no taste_value
        if ($this->taste_level) {
            $levelLabels = [
                1 => 'Mild',
                2 => 'Light',
                3 => 'Medium',
                4 => 'Strong',
                5 => 'Intense'
            ];
            $levelLabel = $levelLabels[$this->taste_level] ?? 'Unknown';
            return "{$typeLabel}: {$levelLabel}";
        }

        return $typeLabel;
    }

    public function getTasteIconAttribute()
    {
        if (!$this->taste_type) {
            return null;
        }

        // Simple mapping for sweetness and spiciness only
        return $this->taste_type === 'sweetness' ? '🍯' : '🌶️';
    }

    public function getTasteLevelStarsAttribute()
    {
        if (!$this->taste_level) {
            return '';
        }

        $icon = $this->taste_icon;
        return str_repeat($icon, $this->taste_level);
    }
}