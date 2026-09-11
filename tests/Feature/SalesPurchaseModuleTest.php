<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPurchaseModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_index_shows_outstanding_invoices(): void
    {
        $this->seedOwner();
        $customer = $this->makeCustomer();
        SalesInvoice::create([
            'contact_id' => $customer->id,
            'number' => 'INV-2026-1001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'sent',
            'subtotal' => 1000000,
            'tax_amount' => 110000,
            'total' => 1110000,
            'paid_amount' => 0,
        ]);

        $this->get(route('sales.index'))
            ->assertOk()
            ->assertSee('Penjualan & Piutang')
            ->assertSee('INV-2026-1001')
            ->assertSee('Nusantara Store');
    }

    public function test_owner_can_create_sales_invoice_with_tax(): void
    {
        $this->seedOwner();
        $customer = $this->makeCustomer();
        $tax = TaxRate::create(['name' => 'PPN 11%', 'rate' => 11, 'type' => 'both', 'is_active' => true]);

        $this->post(route('sales.store'), [
            'contact_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 10000000,
            'tax_rate_id' => $tax->id,
            'status' => 'sent',
        ])->assertRedirect(route('sales.index'));

        $this->assertDatabaseHas('sales_invoices', [
            'contact_id' => $customer->id,
            'subtotal' => 10000000,
            'tax_amount' => 1100000,
            'total' => 11100000,
            'status' => 'sent',
        ]);
    }

    public function test_owner_can_record_receivable_payment(): void
    {
        $this->seedOwner();
        $customer = $this->makeCustomer();
        $invoice = SalesInvoice::create([
            'contact_id' => $customer->id,
            'number' => 'INV-2026-1002',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'sent',
            'subtotal' => 1000000,
            'tax_amount' => 0,
            'total' => 1000000,
            'paid_amount' => 0,
        ]);

        $this->post(route('sales.pay', $invoice), ['amount' => 400000])
            ->assertRedirect(route('sales.index'));

        $invoice->refresh();
        $this->assertSame('partial', $invoice->status);
        $this->assertEquals(400000, (float) $invoice->paid_amount);
        $this->assertEquals(600000, $invoice->outstandingAmount());
    }

    public function test_owner_can_create_purchase_bill_and_pay(): void
    {
        $this->seedOwner();
        $vendor = $this->makeVendor();

        $this->post(route('purchases.store'), [
            'contact_id' => $vendor->id,
            'bill_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(20)->format('Y-m-d'),
            'subtotal' => 5000000,
            'status' => 'received',
        ])->assertRedirect(route('purchases.index'));

        $bill = PurchaseBill::query()->first();
        $this->assertNotNull($bill);

        $this->post(route('purchases.pay', $bill), ['amount' => 5000000])
            ->assertRedirect(route('purchases.index'));

        $bill->refresh();
        $this->assertSame('paid', $bill->status);
        $this->assertEquals(0, $bill->outstandingAmount());
    }

    public function test_viewer_cannot_create_sales_invoice(): void
    {
        $viewer = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@kabarbanten.test',
            'password' => 'password',
            'role' => 'viewer',
        ]);
        $this->actingAs($viewer);
        $customer = $this->makeCustomer();

        $this->post(route('sales.store'), [
            'contact_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 1000000,
            'status' => 'sent',
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

    private function makeCustomer(): Contact
    {
        return Contact::create([
            'type' => 'customer',
            'name' => 'Nusantara Store',
            'email' => 'finance@nusantarastore.test',
        ]);
    }

    private function makeVendor(): Contact
    {
        return Contact::create([
            'type' => 'vendor',
            'name' => 'Mitra Usaha Abadi',
            'email' => 'sales@mitrausaha.test',
        ]);
    }
}
