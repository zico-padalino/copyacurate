<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\RolePermission;
use App\Support\PermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->currentUser();
        abort_unless($user->canManageRoles(), 403);

        if (RolePermission::query()->count() === 0) {
            PermissionCatalog::syncDefaults();
        }

        $catalog = PermissionCatalog::all();
        $roles = PermissionCatalog::roles();
        $matrix = [];

        foreach ($roles as $role) {
            $matrix[$role] = RolePermission::mapForRole($role);
            if ($matrix[$role] === []) {
                $matrix[$role] = PermissionCatalog::defaultsFor($role);
            }
        }

        $grouped = collect($catalog)->groupBy('group');

        return view('roles.index', compact('user', 'catalog', 'roles', 'matrix', 'grouped'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($this->currentUser()->canManageRoles(), 403, 'Anda tidak dapat mengatur hak akses role.');

        $data = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['array'],
        ]);

        foreach (PermissionCatalog::roles() as $role) {
            foreach (PermissionCatalog::keys() as $permission) {
                $allowed = (bool) data_get($data, "permissions.$role.$permission", false);

                if ($role === 'owner' && in_array($permission, PermissionCatalog::protectedOwnerPermissions(), true)) {
                    $allowed = true;
                }

                RolePermission::query()->updateOrCreate(
                    ['role' => $role, 'permission' => $permission],
                    ['allowed' => $allowed],
                );
            }

            RolePermission::forgetRoleCache($role);
        }

        return redirect()->route('roles.index')->with('success', 'Hak akses tiap role berhasil disimpan.');
    }

    public function reset(): RedirectResponse
    {
        abort_unless($this->currentUser()->canManageRoles(), 403);
        PermissionCatalog::syncDefaults();

        return redirect()->route('roles.index')->with('success', 'Hak akses dikembalikan ke default.');
    }
}
