<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Account;
use App\Models\CashAccount;
use App\Models\FixedAsset;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use Illuminate\View\View;

class ReportController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $accounts = Account::query()->orderBy('code')->get();

        $revenue = (float) JournalEntry::query()
            ->whereHas('creditAccount', fn ($query) => $query->where('type', 'revenue'))
            ->sum('amount');
        $expense = (float) JournalEntry::query()
            ->whereHas('debitAccount', fn ($query) => $query->where('type', 'expense'))
            ->sum('amount');

        $incomeStatement = [
            'revenue' => $revenue,
            'expense' => $expense,
            'net_income' => $revenue - $expense,
        ];

        $trialBalance = $accounts->map(fn (Account $account) => [
            'code' => $account->code,
            'name' => $account->name,
            'type' => $account->typeLabel(),
            'balance' => $account->balance(),
        ]);

        $balanceSheet = [
            'assets' => (float) $accounts->where('type', 'asset')->sum(fn (Account $account) => $account->balance())
                + (float) CashAccount::query()->sum('balance')
                + (float) SalesInvoice::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount')
                + (float) Product::query()->get()->sum(fn (Product $product) => (float) $product->stock * (float) $product->buying_price)
                + (float) FixedAsset::query()->where('status', 'active')->get()->sum(fn (FixedAsset $asset) => $asset->bookValue()),
            'liabilities' => (float) $accounts->where('type', 'liability')->sum(fn (Account $account) => $account->balance())
                + (float) PurchaseBill::open()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'equity' => (float) $accounts->where('type', 'equity')->sum(fn (Account $account) => $account->balance()),
        ];
        $balanceSheet['equity'] += $incomeStatement['net_income'];

        $receivableAging = [
            'current' => (float) SalesInvoice::open()->whereDate('due_date', '>=', now())->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'overdue' => (float) SalesInvoice::open()->whereDate('due_date', '<', now())->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
        ];
        $payableAging = [
            'current' => (float) PurchaseBill::open()->whereDate('due_date', '>=', now())->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
            'overdue' => (float) PurchaseBill::open()->whereDate('due_date', '<', now())->selectRaw('COALESCE(SUM(total - paid_amount), 0) as amount')->value('amount'),
        ];

        return view('reports.index', compact(
            'user',
            'incomeStatement',
            'trialBalance',
            'balanceSheet',
            'receivableAging',
            'payableAging',
        ));
    }
}
