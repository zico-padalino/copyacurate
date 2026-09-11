@extends('layouts.app')
@section('title', 'Daftar Akun')
@section('crumb') Workspace / <strong>Daftar Akun</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Chart of accounts</div><h1>Daftar Akun</h1><p>Struktur akun keuangan untuk jurnal dan laporan.</p></div>@if($user->canManage('accounts'))<a class="button" href="#buat">+ Akun Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Akun</div><div class="card-value">{{ $stats['total'] }}</div><span class="muted">COA</span></div>
<div class="card"><div class="card-label">Aset</div><div class="card-value">{{ $stats['assets'] }}</div><span class="muted">Asset accounts</span></div>
<div class="card"><div class="card-label">Liabilitas</div><div class="card-value">{{ $stats['liabilities'] }}</div><span class="muted">Liability</span></div>
<div class="card"><div class="card-label">Ekuitas</div><div class="card-value">{{ $stats['equity'] }}</div><span class="muted">Equity</span></div>
</div>
@if($user->canManage('accounts'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Akun</h2></div>
<form method="POST" action="{{ route('accounts.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Kode</label><input name="code" placeholder="1200" required></div>
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Tipe</label><select name="type" required><option value="asset">Aset</option><option value="liability">Liabilitas</option><option value="equity">Ekuitas</option><option value="revenue">Pendapatan</option><option value="expense">Beban</option></select></div>
<div><label class="muted">Saldo Awal</label><input class="js-rupiah" name="opening_balance" value="0" required></div>
<div class="full form-actions"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Chart of Accounts</h2><a class="panel-link" href="{{ route('journals.index') }}">Ke jurnal →</a></div>
<table class="table"><thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th class="num">Saldo Awal</th><th class="num">Saldo Berjalan</th><th class="num">Jurnal</th></tr></thead>
<tbody>@forelse($accounts as $account)<tr>
<td>{{ $account->code }}</td><td>{{ $account->name }}</td>
<td><span class="tag">{{ $account->typeLabel() }}</span></td>
<td class="num">Rp {{ number_format($account->opening_balance, 0, ',', '.') }}</td>
<td class="num">Rp {{ number_format($account->balance(), 0, ',', '.') }}</td>
<td class="num">{{ $account->debit_entries_count + $account->credit_entries_count }}</td>
</tr>@empty<tr><td colspan="6" class="empty">Belum ada akun.</td></tr>@endforelse</tbody></table></section>
@endsection
