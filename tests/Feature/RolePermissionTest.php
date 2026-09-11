<?php

namespace Tests\Feature;

use App\Models\RolePermission;
use App\Models\User;
use App\Support\PermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_and_update_role_permissions(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        PermissionCatalog::syncDefaults();

        $this->actingAs($owner)->get(route('roles.index'))
            ->assertOk()
            ->assertSee('Hak Akses Role');

        $payload = ['permissions' => []];
        foreach (PermissionCatalog::roles() as $role) {
            foreach (PermissionCatalog::keys() as $key) {
                if ($role === 'owner') {
                    $payload['permissions'][$role][$key] = '1';
                } elseif ($role === 'accountant' && in_array($key, ['dashboard_view', 'sales_view', 'sales_manage'], true)) {
                    $payload['permissions'][$role][$key] = '1';
                } elseif ($role === 'viewer' && str_ends_with($key, '_view')) {
                    $payload['permissions'][$role][$key] = '1';
                }
            }
        }

        $this->actingAs($owner)->post(route('roles.update'), $payload)
            ->assertRedirect(route('roles.index'));

        RolePermission::forgetAllRoleCaches();

        $accountant = User::factory()->create(['role' => 'accountant']);
        $this->assertTrue($accountant->hasPermission('sales_manage'));
        $this->assertFalse($accountant->hasPermission('products_view'));
    }

    public function test_viewer_cannot_manage_sales_when_only_view_allowed(): void
    {
        PermissionCatalog::syncDefaults();
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($viewer)->get(route('sales.index'))->assertOk();
        $this->actingAs($viewer)->post(route('sales.store'), [
            'contact_id' => 1,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDay()->format('Y-m-d'),
            'subtotal' => 1000,
            'status' => 'sent',
        ])->assertForbidden();
    }

    public function test_sidebar_hides_modules_without_access(): void
    {
        PermissionCatalog::syncDefaults();

        RolePermission::query()->where('role', 'viewer')->update(['allowed' => false]);
        RolePermission::query()->updateOrCreate(
            ['role' => 'viewer', 'permission' => 'dashboard_view'],
            ['allowed' => true],
        );
        RolePermission::forgetRoleCache('viewer');

        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($viewer)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan')
            ->assertDontSee('Penjualan & Piutang');

        $this->actingAs($viewer)->get(route('sales.index'))->assertForbidden();
    }
}
