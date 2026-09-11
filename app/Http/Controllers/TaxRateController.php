<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxRateController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $taxRates = TaxRate::query()->latest('id')->get();
        $salesTax = (float) SalesInvoice::query()->sum('tax_amount');
        $purchaseTax = (float) PurchaseBill::query()->sum('tax_amount');
        $stats = [
            'active' => $taxRates->where('is_active', true)->count(),
            'sales_tax' => $salesTax,
            'purchase_tax' => $purchaseTax,
            'net_vat' => $salesTax - $purchaseTax,
        ];

        return view('taxes.index', compact('user', 'taxRates', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('taxes'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', 'in:sales,purchase,both'],
        ]);

        $data['is_active'] = true;
        TaxRate::query()->create($data);

        return redirect()->route('taxes.index')->with('success', 'Tarif pajak berhasil ditambahkan.');
    }

    public function toggle(TaxRate $taxRate): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('taxes'), 403);
        $taxRate->update(['is_active' => ! $taxRate->is_active]);

        return redirect()->route('taxes.index')->with('success', 'Status tarif pajak diperbarui.');
    }
}
