<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\CashAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashAccountController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $accounts = CashAccount::query()->latest('id')->get();
        $stats = [
            'total' => $accounts->count(),
            'balance' => (float) $accounts->sum('balance'),
            'banks' => $accounts->where('type', 'bank')->count(),
            'cash' => $accounts->where('type', 'cash')->sum('balance'),
        ];

        return view('cash.index', compact('user', 'accounts', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('cash'), 403);
        $request->merge(['balance' => $this->sanitizeMoney($request->input('balance'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:cash,bank,e_wallet'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        CashAccount::query()->create($data);

        return redirect()->route('cash.index')->with('success', 'Rekening kas/bank berhasil ditambahkan.');
    }

    public function adjust(Request $request, CashAccount $cashAccount): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('cash'), 403);
        $request->merge(['amount' => preg_replace('/[^\d\-]/', '', (string) $request->input('amount'))]);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'not_in:0'],
        ]);

        $cashAccount->adjustBalance((float) $data['amount']);

        return redirect()->route('cash.index')->with('success', 'Saldo '.$cashAccount->name.' diperbarui.');
    }

    public function transfer(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('cash'), 403);
        $request->merge(['amount' => $this->sanitizeMoney($request->input('amount'))]);

        $data = $request->validate([
            'from_id' => ['required', 'exists:cash_accounts,id'],
            'to_id' => ['required', 'different:from_id', 'exists:cash_accounts,id'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        DB::transaction(function () use ($data): void {
            $from = CashAccount::query()->lockForUpdate()->findOrFail($data['from_id']);
            $to = CashAccount::query()->lockForUpdate()->findOrFail($data['to_id']);
            abort_if((float) $from->balance < (float) $data['amount'], 422, 'Saldo sumber tidak mencukupi.');
            $from->adjustBalance(-1 * (float) $data['amount']);
            $to->adjustBalance((float) $data['amount']);
        });

        return redirect()->route('cash.index')->with('success', 'Transfer antar rekening berhasil.');
    }
}
