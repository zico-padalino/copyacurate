<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Contact;
use App\Models\PurchaseBill;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseBillController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $bills = PurchaseBill::with('contact')->latest('bill_date')->latest('id')->get();
        $vendors = Contact::query()
            ->whereIn('type', ['vendor', 'both'])
            ->orderBy('name')
            ->get();
        $taxRates = TaxRate::query()->where('is_active', true)->whereIn('type', ['purchase', 'both'])->orderBy('name')->get();

        $stats = [
            'open_count' => PurchaseBill::open()->count(),
            'outstanding' => (float) PurchaseBill::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'overdue' => (float) PurchaseBill::query()
                ->open()
                ->where(function ($query): void {
                    $query->where('status', 'overdue')
                        ->orWhereDate('due_date', '<', now());
                })
                ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')
                ->value('amount'),
            'paid' => (float) PurchaseBill::sum('paid_amount'),
        ];

        return view('purchases.index', compact('user', 'bills', 'vendors', 'taxRates', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->demoUser();
        abort_unless($user->canManage('purchases'), 403, 'Role ini tidak dapat membuat tagihan pembelian.');

        $request->merge([
            'subtotal' => preg_replace('/\D+/', '', (string) $request->input('subtotal')),
        ]);

        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:bill_date'],
            'subtotal' => ['required', 'numeric', 'min:1'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'status' => ['required', 'in:draft,received'],
        ]);

        $vendor = Contact::query()->findOrFail($data['contact_id']);
        abort_unless($vendor->isVendor(), 422, 'Kontak yang dipilih bukan vendor.');

        $subtotal = (float) $data['subtotal'];
        $taxRate = isset($data['tax_rate_id']) ? TaxRate::query()->find($data['tax_rate_id']) : null;
        $taxAmount = $taxRate ? round($subtotal * ((float) $taxRate->rate / 100), 2) : 0.0;

        PurchaseBill::create([
            'contact_id' => $vendor->id,
            'number' => PurchaseBill::nextNumber(),
            'bill_date' => $data['bill_date'],
            'due_date' => $data['due_date'],
            'status' => $data['status'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'paid_amount' => 0,
        ]);

        return redirect()->route('purchases.index')->with('success', 'Tagihan pembelian berhasil dibuat.');
    }

    public function pay(Request $request, PurchaseBill $purchaseBill): RedirectResponse
    {
        $user = $this->demoUser();
        abort_unless($user->canManage('purchases'), 403, 'Role ini tidak dapat mencatat pembayaran utang.');
        abort_unless($purchaseBill->isOpen() || $purchaseBill->status === 'draft', 422, 'Tagihan ini tidak dapat dibayar.');

        $request->merge([
            'amount' => preg_replace('/\D+/', '', (string) $request->input('amount')),
        ]);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:'.$purchaseBill->outstandingAmount()],
        ]);

        if ($purchaseBill->status === 'draft') {
            $purchaseBill->status = 'received';
            $purchaseBill->save();
        }

        $purchaseBill->applyPayment((float) $data['amount']);

        return redirect()->route('purchases.index')->with('success', 'Pembayaran utang '.$purchaseBill->number.' berhasil dicatat.');
    }
}
