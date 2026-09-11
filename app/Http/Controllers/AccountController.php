<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $accounts = Account::query()->withCount(['debitEntries', 'creditEntries'])->orderBy('code')->get();
        $stats = [
            'total' => $accounts->count(),
            'assets' => $accounts->where('type', 'asset')->count(),
            'liabilities' => $accounts->where('type', 'liability')->count(),
            'equity' => $accounts->where('type', 'equity')->count(),
        ];

        return view('accounts.index', compact('user', 'accounts', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('accounts'), 403);
        $request->merge(['opening_balance' => $this->sanitizeMoney($request->input('opening_balance'))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ]);

        Account::query()->create($data);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan.');
    }
}
