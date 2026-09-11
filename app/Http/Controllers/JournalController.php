<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $accounts = Account::query()->orderBy('code')->get();
        $entries = JournalEntry::query()
            ->with(['debitAccount', 'creditAccount', 'creator'])
            ->latest('entry_date')
            ->latest('id')
            ->get();

        $stats = [
            'count' => $entries->count(),
            'total' => (float) $entries->sum('amount'),
            'this_month' => (float) JournalEntry::query()
                ->whereMonth('entry_date', now()->month)
                ->whereYear('entry_date', now()->year)
                ->sum('amount'),
            'accounts' => $accounts->count(),
        ];

        return view('journals.index', compact('user', 'accounts', 'entries', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->demoUser();
        abort_unless($user->canManage('journals'), 403, 'Role ini tidak memiliki akses untuk membuat jurnal.');
        $request->merge(['amount' => $this->sanitizeMoney($request->input('amount'))]);

        $data = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:180'],
            'amount' => ['required', 'numeric', 'min:1'],
            'debit_account_id' => ['required', 'exists:accounts,id'],
            'credit_account_id' => ['required', 'different:debit_account_id', 'exists:accounts,id'],
        ]);

        $data['reference'] = 'JV-'.now()->format('ymdHis');
        $data['created_by'] = $user->id;
        JournalEntry::query()->create($data);

        return redirect()->route('journals.index')->with('success', 'Jurnal berhasil dicatat.');
    }
}
