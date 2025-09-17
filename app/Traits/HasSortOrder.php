<?php

namespace App\Traits;

trait HasSortOrder
{
    /**
     * Boot the trait
     */
    protected static function bootHasSortOrder()
    {
        // Before creating a new record
        static::creating(function ($model) {
            $model->adjustSortOrderOnCreate();
        });

        // Before updating an existing record
        static::updating(function ($model) {
            if ($model->isDirty('sort_order')) {
                $model->adjustSortOrderOnUpdate();
            }
        });
    }

    /**
     * Adjust sort order when creating a new record
     */
    protected function adjustSortOrderOnCreate()
    {
        // If no sort_order is provided, set it to the max + 1
        if (is_null($this->sort_order)) {
            $this->sort_order = static::max('sort_order') + 1;
            return;
        }

        // Check if the sort_order already exists
        $existingCount = static::where('sort_order', '>=', $this->sort_order)->count();
        
        if ($existingCount > 0) {
            // Increment sort_order for all items with sort_order >= the new sort_order
            static::where('sort_order', '>=', $this->sort_order)
                ->increment('sort_order');
        }
    }

    /**
     * Adjust sort order when updating an existing record
     */
    protected function adjustSortOrderOnUpdate()
    {
        $originalSortOrder = $this->getOriginal('sort_order');
        $newSortOrder = $this->sort_order;

        // If sort order hasn't changed, do nothing
        if ($originalSortOrder == $newSortOrder) {
            return;
        }

        // Moving up (to a lower number)
        if ($newSortOrder < $originalSortOrder) {
            // Increment items between new and original position
            static::where('id', '!=', $this->id)
                ->where('sort_order', '>=', $newSortOrder)
                ->where('sort_order', '<', $originalSortOrder)
                ->increment('sort_order');
        } 
        // Moving down (to a higher number)
        else {
            // Decrement items between original and new position
            static::where('id', '!=', $this->id)
                ->where('sort_order', '>', $originalSortOrder)
                ->where('sort_order', '<=', $newSortOrder)
                ->decrement('sort_order');
        }
    }

    /**
     * Reorder all items to have sequential sort orders starting from 1
     */
    public static function reorderAll()
    {
        $items = static::orderBy('sort_order')->get();
        
        foreach ($items as $index => $item) {
            $item->sort_order = $index + 1;
            $item->saveQuietly();
        }
    }

    /**
     * Move item to a specific position
     */
    public function moveTo($position)
    {
        $this->sort_order = $position;
        $this->save();
        return $this;
    }

    /**
     * Move item up by one position
     */
    public function moveUp()
    {
        if ($this->sort_order > 1) {
            $this->moveTo($this->sort_order - 1);
        }
        return $this;
    }

    /**
     * Move item down by one position
     */
    public function moveDown()
    {
        $maxOrder = static::max('sort_order');
        if ($this->sort_order < $maxOrder) {
            $this->moveTo($this->sort_order + 1);
        }
        return $this;
    }

    /**
     * Get the next available sort order
     */
    public static function getNextSortOrder()
    {
        return (static::max('sort_order') ?? 0) + 1;
    }
}