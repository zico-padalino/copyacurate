<?php

namespace App\Models;

use App\Support\PermissionCatalog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function hasPermission(string $permission): bool
    {
        if (! in_array($permission, PermissionCatalog::keys(), true)) {
            return false;
        }

        if ($this->role === 'owner' && in_array($permission, PermissionCatalog::protectedOwnerPermissions(), true)) {
            return true;
        }

        $map = RolePermission::mapForRole($this->role);

        if ($map === []) {
            return (bool) (PermissionCatalog::defaultsFor($this->role)[$permission] ?? false);
        }

        return (bool) ($map[$permission] ?? false);
    }

    public function canAccess(string $module): bool
    {
        return $this->hasPermission(PermissionCatalog::key($module, 'view'))
            || $this->hasPermission(PermissionCatalog::key($module, 'manage'));
    }

    public function canManage(string $module): bool
    {
        return $this->hasPermission(PermissionCatalog::key($module, 'manage'));
    }

    public function canPostJournal(): bool
    {
        return $this->canManage('journals');
    }

    public function canManageReceivables(): bool
    {
        return $this->canManage('sales') || $this->canManage('purchases');
    }

    public function canManageMasterData(): bool
    {
        return $this->canManage('products')
            || $this->canManage('contacts')
            || $this->canManage('accounts')
            || $this->canManage('cash')
            || $this->canManage('taxes')
            || $this->canManage('assets')
            || $this->canManage('projects');
    }

    public function canManageUsers(): bool
    {
        return $this->canManage('users');
    }

    public function canManageRoles(): bool
    {
        return $this->canManage('roles');
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
