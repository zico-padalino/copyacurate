@extends('layouts.app')
@section('title', 'Pelanggan & Vendor')
@section('crumb') Operasional / <strong>Pelanggan & Vendor</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Master data</div><h1>Pelanggan & Vendor</h1><p>Kelola kontak pelanggan, vendor, dan mitra bisnis.</p></div>@if($user->canManage('contacts'))<a class="button" href="#buat">+ Kontak Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Total Kontak</div><div class="card-value">{{ $stats['total'] }}</div><span class="muted">Semua tipe</span></div>
<div class="card"><div class="card-label">Pelanggan</div><div class="card-value">{{ $stats['customers'] }}</div><span class="muted">Customer / both</span></div>
<div class="card"><div class="card-label">Vendor</div><div class="card-value">{{ $stats['vendors'] }}</div><span class="muted">Supplier / both</span></div>
<div class="card"><div class="card-label">Faktur Terkait</div><div class="card-value">{{ $stats['invoices'] }}</div><span class="muted">Sales invoices</span></div>
</div>
@if($user->canManage('contacts'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Kontak</h2></div>
<form method="POST" action="{{ route('contacts.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Tipe</label><select name="type" required><option value="customer">Pelanggan</option><option value="vendor">Vendor</option><option value="both">Pelanggan & Vendor</option></select></div>
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Email</label><input type="email" name="email"></div>
<div><label class="muted">Telepon</label><input name="phone"></div>
<div><label class="muted">NPWP</label><input name="tax_number"></div>
<div class="full"><label class="muted">Alamat</label><input name="address"></div>
<div class="full form-actions"><button class="button" type="submit">Simpan Kontak</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Kontak</h2></div>
<table class="table"><thead><tr><th>Nama</th><th>Tipe</th><th>Email</th><th>Telepon</th><th>NPWP</th><th>Faktur</th><th>Tagihan</th></tr></thead>
<tbody>@forelse($contacts as $contact)<tr>
<td>{{ $contact->name }}</td><td><span class="tag">{{ strtoupper($contact->type) }}</span></td>
<td>{{ $contact->email ?: '—' }}</td><td>{{ $contact->phone ?: '—' }}</td><td>{{ $contact->tax_number ?: '—' }}</td>
<td>{{ $contact->sales_invoices_count }}</td><td>{{ $contact->purchase_bills_count }}</td>
</tr>@empty<tr><td colspan="7" class="empty">Belum ada kontak.</td></tr>@endforelse</tbody></table></section>
@endsection
