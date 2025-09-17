<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\Auditable;


class Category extends Model
{
use HasFactory, Auditable;
protected $fillable = ['name','slug','parent_id','description','is_active','position','image'];


public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }
public function children() { return $this->hasMany(Category::class, 'parent_id'); }
public function products() { return $this->hasMany(Product::class); }
public function homePageCategory() { return $this->hasOne(HomePageCategory::class); }

// Image accessor methods
public function getImageUrlAttribute()
{
    if ($this->image) {
        return url('storage/' . $this->image);
    }
    return url('img/placeholder-category.jpg');
}

public function getThumbnailUrlAttribute()
{
    if ($this->image) {
        return asset('storage/' . $this->image);
    }
    return null;
}

    /**
     * Safely delete the category and handle all related data
     */
    public function safeDelete()
    {
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // 1. Handle child categories - move them to parent category
            $this->children()->update(['parent_id' => $this->parent_id]);
            
            // 2. Handle products - set category_id to null
            $this->products()->update(['category_id' => null]);
            
            // 3. Handle hero slides - set category_id to null (now that it's nullable)
            DB::table('hero_slides')->where('category_id', $this->id)->update(['category_id' => null]);
            
            // 4. Delete the category image if it exists
            if ($this->image && Storage::disk('public')->exists($this->image)) {
                Storage::disk('public')->delete($this->image);
            }
            
            // 5. Delete the category
            $this->delete();
            
            // Commit the transaction
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            throw $e;
        }
    }

/**
 * Get the number of products in this category
 */
public function getProductsCountAttribute()
{
    return $this->products()->count();
}

/**
 * Check if category can be safely deleted
 */
public function canBeDeleted()
{
    return true; // With our safe deletion method, any category can be deleted
}
}