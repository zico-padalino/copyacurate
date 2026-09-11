@extends('layouts.app')
@section('title', 'Kas & Bank')
@section('crumb') Keuangan / <strong>Kas & Bank</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Likuiditas</div><h1>Kas & Bank</h1><p>Kelola rekening kas, bank, e-wallet, serta transfer antar rekening.</p></div>@if($user->canManage('cash'))<a class="button" href="#buat">+ Rekening Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Rekening</div><div class="card-value">{{ $stats['total'] }}</div><span class="muted">Aktif</span></div>
<div class="card"><div class="card-label">Total Saldo</div><div class="card-value">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</div><span class="muted">Semua rekening</span></div>
<div class="card"><div class="card-label">Rekening Bank</div><div class="card-value">{{ $stats['banks'] }}</div><span class="muted">Tipe bank</span></div>
<div class="card"><div class="card-label">Saldo Kas Tunai</div><div class="card-value">Rp {{ number_format($stats['cash'], 0, ',', '.') }}</div><span class="muted">Tipe cash</span></div>
</div>
@if($user->canManage('cash'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Rekening</h2></div>
<form method="POST" action="{{ route('cash.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Tipe</label><select name="type" required><option value="bank">Bank</option><option value="cash">Kas</option><option value="e_wallet">E-Wallet</option></select></div>
<div><label class="muted">No. Rekening</label><input name="account_number"></div>
<div><label class="muted">Saldo Awal</label><input class="js-rupiah" name="balance" value="0" required></div>
<div class="full form-actions"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
<section class="panel table-wrap"><div class="panel-head"><h2>Transfer Antar Rekening</h2></div>
<form method="POST" action="{{ route('cash.transfer') }}">@csrf
<div class="form-grid">
<div><label class="muted">Dari</label><select name="from_id" required><option value="">Pilih...</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></div>
<div><label class="muted">Ke</label><select name="to_id" required><option value="">Pilih...</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></div>
<div><label class="muted">Nominal</label><input class="js-rupiah" name="amount" required></div>
<div class="form-actions" style="align-items:end"><button class="button" type="submit">Transfer</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Rekening</h2></div>
<table class="table"><thead><tr><th>Nama</th><th>Tipe</th><th>No. Rekening</th><th class="num">Saldo</th><th class="num">Aksi</th></tr></thead>
<tbody>@forelse($accounts as $account)<tr>
<td>{{ $account->name }}</td><td><span class="tag">{{ $account->typeLabel() }}</span></td>
<td>{{ $account->account_number ?: '—' }}</td>
<td class="num">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
<td class="num">@if($user->canManage('cash'))
<form class="pay-form" method="POST" action="{{ route('cash.adjust', $account) }}">@csrf
<input name="amount" placeholder="+/- 100000" required>
<button class="button small" type="submit">Sesuaikan</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="5" class="empty">Belum ada rekening.</td></tr>@endforelse</tbody></table></section>
@endsection
