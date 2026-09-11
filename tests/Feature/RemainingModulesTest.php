<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashAccount;
use App\Models\FixedAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemainingModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_module_pages_are_reachable(): void
    {
        $this->seedOwner();

        foreach ([
            'contacts.index',
            'products.index',
            'cash.index',
            'taxes.index',
            'assets.index',
            'projects.index',
            'accounts.index',
            'journals.index',
            'reports.index',
            'users.index',
            'integrations.index',
            'settings.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_owner_can_create_contact_product_and_cash_account(): void
    {
        $this->seedOwner();

        $this->post(route('contacts.store'), [
            'type' => 'customer',
            'name' => 'Toko Baru',
            'email' => 'toko@baru.test',
        ])->assertRedirect(route('contacts.index'));

        $this->post(route('products.store'), [
            'sku' => 'PRD-100',
            'name' => 'Mouse Wireless',
            'type' => 'product',
            'selling_price' => '250000',
            'buying_price' => '150000',
            'stock' => '10',
            'minimum_stock' => '2',
            'is_active' => '1',
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'PRD-100', 'selling_price' => 250000]);

        $this->post(route('cash.store'), [
            'name' => 'Bank Mandiri',
            'type' => 'bank',
            'account_number' => '998877',
            'balance' => '5000000',
        ])->assertRedirect(route('cash.index'));

        $this->assertDatabaseHas('cash_accounts', ['name' => 'Bank Mandiri', 'balance' => 5000000]);
    }

    public function test_cash_transfer_and_asset_depreciation_work(): void
    {
        $this->seedOwner();
        $from = CashAccount::create(['name' => 'Kas A', 'type' => 'cash', 'balance' => 1000000]);
        $to = CashAccount::create(['name' => 'Kas B', 'type' => 'cash', 'balance' => 0]);

        $this->post(route('cash.transfer'), [
            'from_id' => $from->id,
            'to_id' => $to->id,
            'amount' => '250000',
        ])->assertRedirect(route('cash.index'));

        $this->assertEquals(750000, (float) $from->fresh()->balance);
        $this->assertEquals(250000, (float) $to->fresh()->balance);

        $asset = FixedAsset::create([
            'asset_code' => 'AST-99',
            'name' => 'Monitor',
            'acquired_at' => now()->subYear(),
            'acquisition_cost' => 12000000,
            'useful_life_months' => 24,
            'accumulated_depreciation' => 0,
            'status' => 'active',
        ]);

        $this->post(route('assets.depreciate', $asset))->assertRedirect(route('assets.index'));
        $this->assertEquals(500000, (float) $asset->fresh()->accumulated_depreciation);
    }

    public function test_owner_can_create_journal_account_tax_and_project(): void
    {
        $this->seedOwner();
        $debit = Account::create(['code' => '1100', 'name' => 'Kas', 'type' => 'asset', 'opening_balance' => 0]);
        $credit = Account::create(['code' => '4100', 'name' => 'Pendapatan', 'type' => 'revenue', 'opening_balance' => 0]);

        $this->post(route('journals.store'), [
            'entry_date' => now()->format('Y-m-d'),
            'description' => 'Penjualan tunai',
            'amount' => '1500000',
            'debit_account_id' => $debit->id,
            'credit_account_id' => $credit->id,
        ])->assertRedirect(route('journals.index'));

        $this->post(route('taxes.store'), [
            'name' => 'PPN 12%',
            'rate' => 12,
            'type' => 'both',
        ])->assertRedirect(route('taxes.index'));

        $this->post(route('projects.store'), [
            'code' => 'PRJ-99',
            'name' => 'Rollout Cabang',
            'start_date' => now()->format('Y-m-d'),
            'budget' => '25000000',
            'status' => 'active',
        ])->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', ['code' => 'PRJ-99']);
        $this->assertDatabaseHas('tax_rates', ['name' => 'PPN 12%']);
    }

    public function test_viewer_cannot_create_master_data(): void
    {
        $viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@kabarbanten.test',
            'password' => 'password',
            'role' => 'viewer',
        ]);
        $this->actingAs($viewer);

        $this->post(route('contacts.store'), [
            'type' => 'customer',
            'name' => 'Blocked',
        ])->assertForbidden();
    }

    private function seedOwner(): User
    {
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@kabarbanten.test',
            'password' => 'password',
            'role' => 'owner',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
