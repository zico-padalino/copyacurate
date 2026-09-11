@extends('layouts.app')
@section('title', 'Jurnal Umum')
@section('crumb') Workspace / <strong>Jurnal Umum</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">General ledger</div><h1>Jurnal Umum</h1><p>Catat transaksi double-entry debit dan kredit.</p></div>@if($user->canManage('journals'))<a class="button" href="#buat">+ Jurnal Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Entri</div><div class="card-value">{{ $stats['count'] }}</div><span class="muted">Semua waktu</span></div>
<div class="card"><div class="card-label">Total Nominal</div><div class="card-value">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div><span class="muted">Akumulasi</span></div>
<div class="card"><div class="card-label">Bulan Ini</div><div class="card-value">Rp {{ number_format($stats['this_month'], 0, ',', '.') }}</div><span class="muted">{{ now()->translatedFormat('F Y') }}</span></div>
<div class="card"><div class="card-label">Akun Tersedia</div><div class="card-value">{{ $stats['accounts'] }}</div><span class="muted"><a class="panel-link" href="{{ route('accounts.index') }}">Kelola COA →</a></span></div>
</div>
@if($user->canManage('journals'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Catat Jurnal</h2><span class="muted">Debit dan kredit harus berbeda akun</span></div>
<form class="journal-form" method="POST" action="{{ route('journals.store') }}">@csrf
<input type="date" name="entry_date" value="{{ now()->format('Y-m-d') }}" required>
<input name="description" placeholder="Deskripsi transaksi" required>
<input class="js-rupiah" name="amount" placeholder="Nominal" required>
<select name="debit_account_id" required><option value="">Debit...</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>@endforeach</select>
<select name="credit_account_id" required><option value="">Kredit...</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>@endforeach</select>
<button class="button" type="submit">Simpan</button>
</form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Riwayat Jurnal</h2><a class="panel-link" href="{{ route('reports.index') }}">Lihat laporan →</a></div>
<table class="table"><thead><tr><th>Tanggal</th><th>Referensi</th><th>Deskripsi</th><th>Debit → Kredit</th><th>Oleh</th><th class="num">Nominal</th></tr></thead>
<tbody>@forelse($entries as $entry)<tr>
<td>{{ $entry->entry_date->format('d M Y') }}</td><td>{{ $entry->reference }}</td><td>{{ $entry->description }}</td>
<td>{{ $entry->debitAccount->name }} → {{ $entry->creditAccount->name }}</td>
<td>{{ $entry->creator?->name ?: '—' }}</td>
<td class="num">Rp {{ number_format($entry->amount, 0, ',', '.') }}</td>
</tr>@empty<tr><td colspan="6" class="empty">Belum ada jurnal.</td></tr>@endforelse</tbody></table></section>
@endsection
