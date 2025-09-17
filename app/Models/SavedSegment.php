<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\UserSegmentService;

class SavedSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'filters',
        'estimated_count',
        'created_by',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateEstimatedCount(): void
    {
        $service = app(UserSegmentService::class);
        $count = $service->buildQuery($this->filters)->count();
        
        $this->update(['estimated_count' => $count]);
    }

    public function getUsers()
    {
        $service = app(UserSegmentService::class);
        return $service->buildQuery($this->filters);
    }
}