@extends('layouts.app')
@section('title', 'Aset Tetap')
@section('crumb') Keuangan / <strong>Aset Tetap</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Kapital</div><h1>Aset Tetap</h1><p>Pencatatan aset, nilai buku, dan penyusutan bulanan garis lurus.</p></div>@if($user->canManage('assets'))<a class="button" href="#buat">+ Aset Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Aset Aktif</div><div class="card-value">{{ $stats['active'] }}</div><span class="muted">Masih digunakan</span></div>
<div class="card"><div class="card-label">Harga Perolehan</div><div class="card-value">Rp {{ number_format($stats['acquisition'], 0, ',', '.') }}</div><span class="muted">Aset aktif</span></div>
<div class="card"><div class="card-label">Nilai Buku</div><div class="card-value">Rp {{ number_format($stats['book_value'], 0, ',', '.') }}</div><span class="muted">Setelah penyusutan</span></div>
<div class="card"><div class="card-label">Akumulasi Penyusutan</div><div class="card-value">Rp {{ number_format($stats['depreciation'], 0, ',', '.') }}</div><span class="muted">Semua aset</span></div>
</div>
@if($user->canManage('assets'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Aset Tetap</h2></div>
<form method="POST" action="{{ route('assets.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Kode Aset</label><input name="asset_code" required></div>
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Tanggal Perolehan</label><input type="date" name="acquired_at" value="{{ now()->format('Y-m-d') }}" required></div>
<div><label class="muted">Harga Perolehan</label><input class="js-rupiah" name="acquisition_cost" required></div>
<div><label class="muted">Umur Manfaat (bulan)</label><input type="number" min="1" name="useful_life_months" value="36" required></div>
<div class="form-actions" style="align-items:end"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Aset</h2></div>
<table class="table"><thead><tr><th>Kode</th><th>Nama</th><th>Perolehan</th><th class="num">Harga</th><th class="num">Penyusutan/bln</th><th class="num">Nilai Buku</th><th>Status</th><th class="num">Aksi</th></tr></thead>
<tbody>@forelse($assets as $asset)<tr>
<td>{{ $asset->asset_code }}</td><td>{{ $asset->name }}</td><td>{{ $asset->acquired_at->format('d M Y') }}</td>
<td class="num">Rp {{ number_format($asset->acquisition_cost, 0, ',', '.') }}</td>
<td class="num">Rp {{ number_format($asset->monthlyDepreciation(), 0, ',', '.') }}</td>
<td class="num">Rp {{ number_format($asset->bookValue(), 0, ',', '.') }}</td>
<td><span class="tag {{ $asset->status === 'active' ? 'sent' : 'draft' }}">{{ strtoupper($asset->status) }}</span></td>
<td class="num">@if($user->canManage('assets') && $asset->status === 'active')
<form method="POST" action="{{ route('assets.depreciate', $asset) }}" style="display:inline">@csrf<button class="button small" type="submit">Susutkan</button></form>
<form method="POST" action="{{ route('assets.dispose', $asset) }}" style="display:inline;margin-left:4px">@csrf<button class="button small ghost" type="submit">Dispose</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="8" class="empty">Belum ada aset tetap.</td></tr>@endforelse</tbody></table></section>
@endsection
