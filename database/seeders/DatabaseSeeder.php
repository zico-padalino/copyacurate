<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashAccount;
use App\Models\Contact;
use App\Models\FixedAsset;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Project;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\TaxRate;
use App\Models\User;
use App\Support\AppSettings;
use App\Support\PermissionCatalog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::updateOrCreate(['email' => 'budi@kabarbanten.test'], [
            'name' => 'Budi Santoso', 'password' => 'password', 'role' => 'owner',
        ]);

        AppSettings::update([
            'app_name' => 'Kabar Banten',
            'company_name' => 'Kabar Banten',
            'tagline' => 'Sistem Keuangan',
        ]);

        PermissionCatalog::syncDefaults();

        $accounts = collect([
            ['code' => '1100', 'name' => 'Kas & Bank', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Piutang Usaha', 'type' => 'asset'],
            ['code' => '1300', 'name' => 'Persediaan Barang', 'type' => 'asset'],
            ['code' => '2100', 'name' => 'Utang Usaha', 'type' => 'liability'],
            ['code' => '3100', 'name' => 'Modal Pemilik', 'type' => 'equity'],
            ['code' => '4100', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],
            ['code' => '5100', 'name' => 'Beban Operasional', 'type' => 'expense'],
        ])->mapWithKeys(fn ($account) => [$account['code'] => Account::updateOrCreate(['code' => $account['code']], $account)]);

        if (JournalEntry::count() === 0) {
            JournalEntry::create(['entry_date' => now()->subDays(2), 'reference' => 'JV-260908001', 'description' => 'Penjualan produk secara tunai', 'amount' => 28500000, 'debit_account_id' => $accounts['1100']->id, 'credit_account_id' => $accounts['4100']->id, 'created_by' => $user->id]);
            JournalEntry::create(['entry_date' => now()->subDay(), 'reference' => 'JV-260909002', 'description' => 'Pembayaran biaya operasional kantor', 'amount' => 4200000, 'debit_account_id' => $accounts['5100']->id, 'credit_account_id' => $accounts['1100']->id, 'created_by' => $user->id]);
        }

        $customer = Contact::updateOrCreate(['email' => 'finance@nusantarastore.test'], ['type' => 'customer', 'name' => 'Nusantara Store', 'phone' => '0812-0000-1111']);
        $vendor = Contact::updateOrCreate(['email' => 'sales@mitrausaha.test'], ['type' => 'vendor', 'name' => 'Mitra Usaha Abadi', 'phone' => '0812-0000-2222']);
        Product::updateOrCreate(['sku' => 'PRD-001'], ['name' => 'Paket Konsultasi Bisnis', 'type' => 'service', 'selling_price' => 7500000, 'buying_price' => 0, 'stock' => 99, 'minimum_stock' => 0]);
        Product::updateOrCreate(['sku' => 'PRD-002'], ['name' => 'Printer Thermal POS', 'type' => 'product', 'selling_price' => 1850000, 'buying_price' => 1200000, 'stock' => 4, 'minimum_stock' => 5]);
        CashAccount::updateOrCreate(['name' => 'Bank BCA Operasional'], ['type' => 'bank', 'account_number' => '1234567890', 'balance' => 284720500]);
        CashAccount::updateOrCreate(['name' => 'Kas Kecil'], ['type' => 'cash', 'balance' => 100000000]);
        TaxRate::updateOrCreate(['name' => 'PPN 11%'], ['rate' => 11, 'type' => 'both']);
        FixedAsset::updateOrCreate(['asset_code' => 'AST-001'], ['name' => 'Laptop Operasional', 'acquired_at' => now()->subMonths(8), 'acquisition_cost' => 18500000, 'useful_life_months' => 36]);
        Project::updateOrCreate(['code' => 'PRJ-2026-01'], ['name' => 'Implementasi Sistem ERP', 'start_date' => now()->startOfMonth(), 'budget' => 85000000, 'status' => 'active']);
        SalesInvoice::updateOrCreate(['number' => 'INV-2026-0001'], ['contact_id' => $customer->id, 'invoice_date' => now()->subDays(3), 'due_date' => now()->addDays(27), 'status' => 'sent', 'subtotal' => 28500000, 'tax_amount' => 3135000, 'total' => 31635000, 'paid_amount' => 0]);
        SalesInvoice::updateOrCreate(['number' => 'INV-2026-0002'], ['contact_id' => $customer->id, 'invoice_date' => now()->subDays(40), 'due_date' => now()->subDays(10), 'status' => 'partial', 'subtotal' => 10000000, 'tax_amount' => 1100000, 'total' => 11100000, 'paid_amount' => 4000000]);
        PurchaseBill::updateOrCreate(['number' => 'BILL-2026-0001'], ['contact_id' => $vendor->id, 'bill_date' => now()->subDays(5), 'due_date' => now()->addDays(25), 'status' => 'received', 'subtotal' => 12000000, 'tax_amount' => 1320000, 'total' => 13320000, 'paid_amount' => 0]);
        PurchaseBill::updateOrCreate(['number' => 'BILL-2026-0002'], ['contact_id' => $vendor->id, 'bill_date' => now()->subDays(20), 'due_date' => now()->subDays(5), 'status' => 'overdue', 'subtotal' => 5000000, 'tax_amount' => 550000, 'total' => 5550000, 'paid_amount' => 0]);
    }
}
