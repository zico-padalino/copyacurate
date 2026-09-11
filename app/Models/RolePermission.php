<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RolePermission extends Model
{
    protected $fillable = ['role', 'permission', 'allowed'];

    protected function casts(): array
    {
        return ['allowed' => 'boolean'];
    }

    public static function isAllowed(string $role, string $permission): bool
    {
        $map = static::mapForRole($role);

        return (bool) ($map[$permission] ?? false);
    }

    /**
     * @return array<string, bool>
     */
    public static function mapForRole(string $role): array
    {
        try {
            return Cache::rememberForever('role_permissions.'.$role, function () use ($role) {
                return static::query()
                    ->where('role', $role)
                    ->pluck('allowed', 'permission')
                    ->map(fn ($allowed) => (bool) $allowed)
                    ->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    public static function forgetRoleCache(string $role): void
    {
        Cache::forget('role_permissions.'.$role);
    }

    public static function forgetAllRoleCaches(): void
    {
        foreach (['owner', 'accountant', 'viewer'] as $role) {
            static::forgetRoleCache($role);
        }
    }
}
