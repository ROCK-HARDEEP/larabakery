<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\Auditable;

class Role extends SpatieRole
{
    use Auditable;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_custom',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the module permissions for this role
     */
    public function modulePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Get the modules this role has access to
     */
    public function modules()
    {
        return $this->belongsToMany(ModulePermission::class, 'role_module_permissions')
            ->withPivot(['can_view', 'can_create', 'can_update', 'can_delete', 'can_export'])
            ->withTimestamps();
    }

    /**
     * Check if role has permission for a module
     */
    public function hasModulePermission($moduleSlug, $permission = 'can_view')
    {
        $module = ModulePermission::where('module_slug', $moduleSlug)->first();

        if (!$module) {
            return false;
        }

        // Superadmin has all permissions
        if ($this->name === 'superadmin') {
            return true;
        }

        $rolePermission = $this->modulePermissions()
            ->where('module_permission_id', $module->id)
            ->first();

        if (!$rolePermission) {
            return false;
        }

        return $rolePermission->$permission ?? false;
    }

    /**
     * Check if role has any permission for a module
     */
    public function hasAnyModulePermission($moduleSlug)
    {
        $module = ModulePermission::where('module_slug', $moduleSlug)->first();

        if (!$module) {
            return false;
        }

        // Superadmin has all permissions
        if ($this->name === 'superadmin') {
            return true;
        }

        $rolePermission = $this->modulePermissions()
            ->where('module_permission_id', $module->id)
            ->first();

        return $rolePermission ? $rolePermission->hasAnyPermission() : false;
    }

    /**
     * Set module permissions for this role
     */
    public function setModulePermissions($moduleId, $permissions)
    {
        return RoleModulePermission::updateOrCreate(
            [
                'role_id' => $this->id,
                'module_permission_id' => $moduleId,
            ],
            $permissions
        );
    }

    /**
     * Copy permissions from another role
     */
    public function copyPermissionsFrom(Role $sourceRole)
    {
        $sourcePermissions = $sourceRole->modulePermissions()->get();

        foreach ($sourcePermissions as $permission) {
            RoleModulePermission::create([
                'role_id' => $this->id,
                'module_permission_id' => $permission->module_permission_id,
                'can_view' => $permission->can_view,
                'can_create' => $permission->can_create,
                'can_update' => $permission->can_update,
                'can_delete' => $permission->can_delete,
                'can_export' => $permission->can_export,
            ]);
        }
    }

    /**
     * Check if this is a system role (non-deletable)
     */
    public function isSystemRole()
    {
        return in_array($this->name, ['superadmin', 'admin']);
    }
}