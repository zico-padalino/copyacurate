<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Contact;
use App\Models\SalesInvoice;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $invoices = SalesInvoice::with('contact')->latest('invoice_date')->latest('id')->get();
        $customers = Contact::query()
            ->whereIn('type', ['customer', 'both'])
            ->orderBy('name')
            ->get();
        $taxRates = TaxRate::query()->where('is_active', true)->whereIn('type', ['sales', 'both'])->orderBy('name')->get();

        $stats = [
            'open_count' => SalesInvoice::open()->count(),
            'outstanding' => (float) SalesInvoice::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'overdue' => (float) SalesInvoice::query()
                ->open()
                ->where(function ($query): void {
                    $query->where('status', 'overdue')
                        ->orWhereDate('due_date', '<', now());
                })
                ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')
                ->value('amount'),
            'collected' => (float) SalesInvoice::sum('paid_amount'),
        ];

        return view('sales.index', compact('user', 'invoices', 'customers', 'taxRates', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->demoUser();
        abort_unless($user->canManage('sales'), 403, 'Role ini tidak dapat membuat faktur penjualan.');

        $request->merge([
            'subtotal' => preg_replace('/\D+/', '', (string) $request->input('subtotal')),
        ]);

        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'subtotal' => ['required', 'numeric', 'min:1'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'status' => ['required', 'in:draft,sent'],
        ]);

        $customer = Contact::query()->findOrFail($data['contact_id']);
        abort_unless($customer->isCustomer(), 422, 'Kontak yang dipilih bukan pelanggan.');

        $subtotal = (float) $data['subtotal'];
        $taxRate = isset($data['tax_rate_id']) ? TaxRate::query()->find($data['tax_rate_id']) : null;
        $taxAmount = $taxRate ? round($subtotal * ((float) $taxRate->rate / 100), 2) : 0.0;

        SalesInvoice::create([
            'contact_id' => $customer->id,
            'number' => SalesInvoice::nextNumber(),
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'status' => $data['status'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'paid_amount' => 0,
        ]);

        return redirect()->route('sales.index')->with('success', 'Faktur penjualan berhasil dibuat.');
    }

    public function pay(Request $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        $user = $this->demoUser();
        abort_unless($user->canManage('sales'), 403, 'Role ini tidak dapat mencatat penerimaan piutang.');
        abort_unless($salesInvoice->isOpen() || $salesInvoice->status === 'draft', 422, 'Faktur ini tidak dapat dibayar.');

        $request->merge([
            'amount' => preg_replace('/\D+/', '', (string) $request->input('amount')),
        ]);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:'.$salesInvoice->outstandingAmount()],
        ]);

        if ($salesInvoice->status === 'draft') {
            $salesInvoice->status = 'sent';
            $salesInvoice->save();
        }

        $salesInvoice->applyPayment((float) $data['amount']);

        return redirect()->route('sales.index')->with('success', 'Pembayaran piutang '.$salesInvoice->number.' berhasil dicatat.');
    }
}
