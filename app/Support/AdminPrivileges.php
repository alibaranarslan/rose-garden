<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPrivileges
{
    public static function canAccessAdminPanel(?User $user): bool
    {
        return (bool) ($user?->is_admin && $user?->is_active);
    }

    public static function canPublishConfiguration(?User $user): bool
    {
        if (! self::canAccessAdminPanel($user)) {
            return false;
        }

        if (! self::rolesAreConfigured()) {
            return true;
        }

        return self::userHasRole($user, 'super_admin');
    }

    private static function rolesAreConfigured(): bool
    {
        try {
            if (! Schema::hasTable('roles') || ! Schema::hasTable('model_has_roles')) {
                return false;
            }

            return DB::table('roles')->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    private static function userHasRole(?User $user, string $role): bool
    {
        if (! $user?->exists) {
            return false;
        }

        $rolesTable = config('permission.table_names.roles', 'roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
        $modelMorphKey = config('permission.column_names.model_morph_key', 'model_id');

        return DB::table($modelHasRolesTable)
            ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
            ->where("{$modelHasRolesTable}.model_type", User::class)
            ->where("{$modelHasRolesTable}.{$modelMorphKey}", $user->getKey())
            ->where("{$rolesTable}.name", $role)
            ->exists();
    }
}
