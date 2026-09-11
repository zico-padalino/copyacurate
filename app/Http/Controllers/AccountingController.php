<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
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
use App\Support\AppSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    use ResolvesDemoUser;

    public function dashboard(): View
    {
        $user = $this->currentUser();
        $accounts = Account::orderBy('code')->get();
        $entries = JournalEntry::with(['debitAccount', 'creditAccount'])->latest('entry_date')->latest()->take(8)->get();
        $revenue = JournalEntry::whereHas('creditAccount', fn ($query) => $query->where('type', 'revenue'))->sum('amount');
        $expense = JournalEntry::whereHas('debitAccount', fn ($query) => $query->where('type', 'expense'))->sum('amount');
        $moduleStats = [
            'contacts' => Contact::count(),
            'products' => Product::count(),
            'lowStock' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'receivables' => (float) SalesInvoice::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'payables' => (float) PurchaseBill::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'cash' => CashAccount::sum('balance'),
            'taxRates' => TaxRate::where('is_active', true)->count(),
            'assets' => FixedAsset::where('status', 'active')->sum('acquisition_cost'),
            'projects' => Project::where('status', 'active')->count(),
        ];
        $companyName = AppSettings::companyName();

        return view('dashboard', compact('user', 'accounts', 'entries', 'revenue', 'expense', 'moduleStats', 'companyName'));
    }

    public function storeJournal(Request $request): RedirectResponse
    {
        $user = $this->currentUser();
        abort_unless($user->canManage('journals'), 403, 'Role ini tidak memiliki akses untuk membuat jurnal.');
        $request->merge([
            'amount' => $this->sanitizeMoney($request->input('amount')),
        ]);

        $data = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:180'],
            'amount' => ['required', 'numeric', 'min:1'],
            'debit_account_id' => ['required', 'exists:accounts,id'],
            'credit_account_id' => ['required', 'different:debit_account_id', 'exists:accounts,id'],
        ]);
        $data['reference'] = 'JV-'.now()->format('ymdHis');
        $data['created_by'] = $user->id;
        JournalEntry::create($data);

        return back()->with('success', 'Jurnal berhasil dicatat.');
    }
}
