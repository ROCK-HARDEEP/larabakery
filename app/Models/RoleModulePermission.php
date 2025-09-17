<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use App\Traits\Auditable;

class RoleModulePermission extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'role_id',
        'module_permission_id',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
        'can_export',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
    ];

    /**
     * Get the role that owns this permission
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the module permission
     */
    public function modulePermission()
    {
        return $this->belongsTo(ModulePermission::class);
    }

    /**
     * Get all permissions as array
     */
    public function getPermissionsArray()
    {
        return [
            'view' => $this->can_view,
            'create' => $this->can_create,
            'update' => $this->can_update,
            'delete' => $this->can_delete,
            'export' => $this->can_export,
        ];
    }

    /**
     * Check if has any permission
     */
    public function hasAnyPermission()
    {
        return $this->can_view || $this->can_create ||
               $this->can_update || $this->can_delete ||
               $this->can_export;
    }
}