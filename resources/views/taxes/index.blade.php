@extends('layouts.app')
@section('title', 'Pajak')
@section('crumb') Keuangan / <strong>Pajak</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Kepatuhan pajak</div><h1>Pajak</h1><p>Kelola tarif PPN dan pantau pajak keluaran vs masukan.</p></div>@if($user->canManage('taxes'))<a class="button" href="#buat">+ Tarif Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Tarif Aktif</div><div class="card-value">{{ $stats['active'] }}</div><span class="muted">Siap dipakai</span></div>
<div class="card"><div class="card-label">Pajak Keluaran</div><div class="card-value">Rp {{ number_format($stats['sales_tax'], 0, ',', '.') }}</div><span class="muted">Dari penjualan</span></div>
<div class="card"><div class="card-label">Pajak Masukan</div><div class="card-value">Rp {{ number_format($stats['purchase_tax'], 0, ',', '.') }}</div><span class="muted">Dari pembelian</span></div>
<div class="card"><div class="card-label">PPN Neto</div><div class="card-value">Rp {{ number_format($stats['net_vat'], 0, ',', '.') }}</div><span class="muted">Keluaran − masukan</span></div>
</div>
@if($user->canManage('taxes'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Tarif Pajak</h2></div>
<form method="POST" action="{{ route('taxes.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Nama</label><input name="name" placeholder="PPN 12%" required></div>
<div><label class="muted">Tarif (%)</label><input type="number" step="0.01" min="0" max="100" name="rate" required></div>
<div><label class="muted">Tipe</label><select name="type" required><option value="both">Penjualan & Pembelian</option><option value="sales">Penjualan</option><option value="purchase">Pembelian</option></select></div>
<div class="form-actions" style="align-items:end"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Tarif</h2></div>
<table class="table"><thead><tr><th>Nama</th><th>Tarif</th><th>Tipe</th><th>Status</th><th class="num">Aksi</th></tr></thead>
<tbody>@forelse($taxRates as $tax)<tr>
<td>{{ $tax->name }}</td><td>{{ number_format($tax->rate, 2, ',', '.') }}%</td>
<td><span class="tag">{{ strtoupper($tax->type) }}</span></td>
<td><span class="tag {{ $tax->is_active ? 'sent' : 'draft' }}">{{ $tax->is_active ? 'AKTIF' : 'NONAKTIF' }}</span></td>
<td class="num">@if($user->canManage('taxes'))
<form method="POST" action="{{ route('taxes.toggle', $tax) }}">@csrf<button class="button small" type="submit">Toggle</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="5" class="empty">Belum ada tarif pajak.</td></tr>@endforelse</tbody></table></section>
@endsection
