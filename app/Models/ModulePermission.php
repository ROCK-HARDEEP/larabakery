<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ModulePermission extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'module_name',
        'module_slug',
        'resource_class',
        'page_class',
        'group',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the role permissions for this module
     */
    public function rolePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Get the roles that have permissions for this module
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_module_permissions')
            ->withPivot(['can_view', 'can_create', 'can_update', 'can_delete', 'can_export'])
            ->withTimestamps();
    }

    /**
     * Check if a role has a specific permission for this module
     */
    public function hasPermission($role, $permission)
    {
        $rolePermission = $this->rolePermissions()
            ->where('role_id', $role->id)
            ->first();

        if (!$rolePermission) {
            return false;
        }

        return $rolePermission->$permission ?? false;
    }

    /**
     * Get all modules grouped by their group
     */
    public static function getGroupedModules()
    {
        return self::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('module_name')
            ->get()
            ->groupBy('group');
    }
}