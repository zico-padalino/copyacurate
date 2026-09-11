<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $users = User::query()->orderBy('name')->get();
        $stats = [
            'total' => $users->count(),
            'owners' => $users->where('role', 'owner')->count(),
            'accountants' => $users->where('role', 'accountant')->count(),
            'viewers' => $users->where('role', 'viewer')->count(),
        ];

        return view('users.index', compact('user', 'users', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManageUsers(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:owner,accountant,viewer'],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::query()->create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function updateRole(Request $request, User $managedUser): RedirectResponse
    {
        abort_unless($this->demoUser()->canManageUsers(), 403);

        $data = $request->validate([
            'role' => ['required', 'in:owner,accountant,viewer'],
        ]);

        $managedUser->update($data);

        return redirect()->route('users.index')->with('success', 'Role '.$managedUser->name.' diperbarui.');
    }
}
