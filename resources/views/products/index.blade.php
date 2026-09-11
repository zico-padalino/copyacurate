@extends('layouts.app')
@section('title', 'Produk & Persediaan')
@section('crumb') Operasional / <strong>Produk & Persediaan</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Inventori</div><h1>Produk & Persediaan</h1><p>Kelola SKU, harga jual/beli, dan penyesuaian stok.</p></div>@if($user->canManage('products'))<a class="button" href="#buat">+ Produk Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Produk</div><div class="card-value">{{ $stats['total'] }}</div><span class="muted">Semua item</span></div>
<div class="card"><div class="card-label">Aktif</div><div class="card-value">{{ $stats['active'] }}</div><span class="muted">Dapat dijual</span></div>
<div class="card"><div class="card-label">Stok Menipis</div><div class="card-value">{{ $stats['low_stock'] }}</div><span class="muted">Di bawah minimum</span></div>
<div class="card"><div class="card-label">Nilai Persediaan</div><div class="card-value">Rp {{ number_format($stats['inventory_value'], 0, ',', '.') }}</div><span class="muted">Stok × harga beli</span></div>
</div>
@if($user->canManage('products'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Produk</h2></div>
<form method="POST" action="{{ route('products.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">SKU</label><input name="sku" required></div>
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Tipe</label><select name="type" required><option value="product">Produk</option><option value="service">Jasa</option></select></div>
<div><label class="muted">Harga Jual</label><input class="js-rupiah" name="selling_price" required></div>
<div><label class="muted">Harga Beli</label><input class="js-rupiah" name="buying_price" value="0" required></div>
<div><label class="muted">Stok Awal</label><input class="js-rupiah" name="stock" value="0" required></div>
<div><label class="muted">Stok Minimum</label><input class="js-rupiah" name="minimum_stock" value="0" required></div>
<div><label class="muted">Status</label><select name="is_active"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
<div class="full form-actions"><button class="button" type="submit">Simpan Produk</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Produk</h2></div>
<table class="table"><thead><tr><th>SKU</th><th>Nama</th><th>Tipe</th><th class="num">Jual</th><th class="num">Beli</th><th class="num">Stok</th><th>Status</th><th class="num">Aksi</th></tr></thead>
<tbody>@forelse($products as $product)<tr>
<td>{{ $product->sku }}</td><td>{{ $product->name }}@if($product->isLowStock()) <span class="tag overdue">LOW</span>@endif</td>
<td><span class="tag">{{ strtoupper($product->type) }}</span></td>
<td class="num">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
<td class="num">Rp {{ number_format($product->buying_price, 0, ',', '.') }}</td>
<td class="num">{{ number_format($product->stock, 0, ',', '.') }}</td>
<td><span class="tag {{ $product->is_active ? 'sent' : 'draft' }}">{{ $product->is_active ? 'AKTIF' : 'NONAKTIF' }}</span></td>
<td class="num">@if($user->canManage('products'))
<form class="pay-form" method="POST" action="{{ route('products.stock', $product) }}">@csrf
<input name="quantity" placeholder="+/- stok" required>
<button class="button small" type="submit">Stok</button></form>
<form method="POST" action="{{ route('products.toggle', $product) }}" style="margin-top:6px">@csrf<button class="button small ghost" type="submit">Toggle</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="8" class="empty">Belum ada produk.</td></tr>@endforelse</tbody></table></section>
@endsection
