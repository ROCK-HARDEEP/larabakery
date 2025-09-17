<?php

namespace App\Policies;

use App\Models\CustomSection;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function view(User $user, CustomSection $customSection): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function update(User $user, CustomSection $customSection): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function delete(User $user, CustomSection $customSection): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function restore(User $user, CustomSection $customSection): bool
    {
        return $user->hasAnyRole(['publisher', 'admin']);
    }

    public function forceDelete(User $user, CustomSection $customSection): bool
    {
        return $user->hasRole('admin');
    }

    public function publish(User $user, CustomSection $customSection): bool
    {
        return $user->hasAnyRole(['publisher', 'admin']);
    }

    public function reorder(User $user): bool
    {
        return $user->hasAnyRole(['content-editor', 'publisher', 'admin']);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasAnyRole(['publisher', 'admin']);
    }

    public function bulkPublish(User $user): bool
    {
        return $user->hasAnyRole(['publisher', 'admin']);
    }
}