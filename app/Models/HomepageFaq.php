<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageFaq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'order_index',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index', 'asc')->orderBy('id', 'asc');
    }
}
