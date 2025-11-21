<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if authenticated user has permission
     */
    public static function can(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission($permission);
    }

    /**
     * Check if authenticated user has role
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole($role);
    }
}

