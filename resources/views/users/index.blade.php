@extends('layouts.app')
@section('title', 'Pengguna & Role')
@section('crumb') Pengaturan / <strong>Pengguna & Role</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Akses</div><h1>Pengguna & Role</h1><p>Kelola akun internal.@if($user->canManageRoles()) Atur izin modul di <a class="panel-link" href="{{ route('roles.index') }}">Hak Akses Role</a>.@endif</p></div>@if($user->canManageUsers())<a class="button" href="#buat">+ Pengguna Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Pengguna</div><div class="card-value">{{ $stats['total'] }}</div><span class="muted">Aktif</span></div>
<div class="card"><div class="card-label">Owner</div><div class="card-value">{{ $stats['owners'] }}</div><span class="muted">Akses penuh</span></div>
<div class="card"><div class="card-label">Akuntan</div><div class="card-value">{{ $stats['accountants'] }}</div><span class="muted">Operasional keuangan</span></div>
<div class="card"><div class="card-label">Viewer</div><div class="card-value">{{ $stats['viewers'] }}</div><span class="muted">Hanya lihat</span></div>
</div>
@if($user->canManageUsers())
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Pengguna</h2></div>
<form method="POST" action="{{ route('users.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Email</label><input type="email" name="email" required></div>
<div><label class="muted">Password</label><input type="password" name="password" minlength="6" required></div>
<div><label class="muted">Role</label><select name="role" required><option value="viewer">Viewer</option><option value="accountant">Akuntan</option><option value="owner">Owner</option></select></div>
<div class="full form-actions"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Pengguna</h2></div>
<table class="table"><thead><tr><th>Nama</th><th>Email</th><th>Role</th><th class="num">Ubah Role</th></tr></thead>
<tbody>@forelse($users as $managed)<tr>
<td>{{ $managed->name }}@if($managed->id === $user->id) <span class="muted">(Anda)</span>@endif</td>
<td>{{ $managed->email }}</td>
<td><span class="tag">{{ strtoupper($managed->role) }}</span></td>
<td class="num">@if($user->canManageUsers())
<form class="pay-form" method="POST" action="{{ route('users.role', $managed) }}">@csrf
<select name="role"><option value="owner" @selected($managed->role==='owner')>Owner</option><option value="accountant" @selected($managed->role==='accountant')>Akuntan</option><option value="viewer" @selected($managed->role==='viewer')>Viewer</option></select>
<button class="button small" type="submit">Update</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="4" class="empty">Belum ada pengguna.</td></tr>@endforelse</tbody></table></section>
@endsection
