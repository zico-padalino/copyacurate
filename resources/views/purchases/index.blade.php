@extends('layouts.app')

@section('title', 'Pembelian & Utang')
@section('crumb')
    Operasional / <strong>Pembelian & Utang</strong>
@endsection

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Modul utang</div>
        <h1>Pembelian & Utang</h1>
        <p>Kelola tagihan vendor, jatuh tempo, dan pembayaran utang usaha.</p>
    </div>
    @if ($user->canManage('purchases'))
        <a class="button" href="#buat-tagihan">+ Tagihan Baru</a>
    @endif
</div>

<div class="cards">
    <div class="card">
        <div class="card-label">Tagihan Berjalan</div>
        <div class="card-value">{{ $stats['open_count'] }}</div>
        <span class="muted">Belum lunas</span>
    </div>
    <div class="card">
        <div class="card-label">Utang Outstanding</div>
        <div class="card-value">Rp {{ number_format($stats['outstanding'], 0, ',', '.') }}</div>
        <span class="muted">Total − terbayar</span>
    </div>
    <div class="card">
        <div class="card-label">Jatuh Tempo / Overdue</div>
        <div class="card-value">Rp {{ number_format($stats['overdue'], 0, ',', '.') }}</div>
        <span class="muted">Perlu dibayar</span>
    </div>
    <div class="card">
        <div class="card-label">Sudah Dibayar</div>
        <div class="card-value">Rp {{ number_format($stats['paid'], 0, ',', '.') }}</div>
        <span class="muted">Akumulasi pembayaran</span>
    </div>
</div>

@if ($user->canManage('purchases'))
<section class="panel table-wrap" id="buat-tagihan">
    <div class="panel-head">
        <h2>Buat Tagihan Pembelian</h2>
        <span class="muted">Otomatis hitung PPN bila dipilih</span>
    </div>
    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <label class="muted" for="contact_id">Vendor</label>
                <select id="contact_id" name="contact_id" required>
                    <option value="">Pilih vendor...</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(old('contact_id') == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="muted" for="bill_date">Tanggal Tagihan</label>
                <input id="bill_date" type="date" name="bill_date" value="{{ old('bill_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="muted" for="due_date">Jatuh Tempo</label>
                <input id="due_date" type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="muted" for="subtotal">Subtotal (Rp)</label>
                <input id="subtotal" class="js-rupiah" type="text" name="subtotal" value="{{ old('subtotal') }}" placeholder="Contoh: 8.000.000" required>
            </div>
            <div>
                <label class="muted" for="tax_rate_id">Pajak</label>
                <select id="tax_rate_id" name="tax_rate_id">
                    <option value="">Tanpa pajak</option>
                    @foreach ($taxRates as $taxRate)
                        <option value="{{ $taxRate->id }}" @selected(old('tax_rate_id') == $taxRate->id)>{{ $taxRate->name }} ({{ $taxRate->rate }}%)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="muted" for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="received" @selected(old('status', 'received') === 'received')>Diterima / Utang</option>
                    <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                </select>
            </div>
            <div class="full form-actions">
                <button class="button" type="submit">Simpan Tagihan</button>
            </div>
        </div>
    </form>
</section>
@endif

<section class="panel table-wrap">
    <div class="panel-head">
        <h2>Daftar Tagihan</h2>
        <a class="panel-link" href="{{ route('sales.index') }}">Lihat piutang →</a>
    </div>
    <div class="table-wrap" style="margin-top:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Vendor</th>
                    <th>Tanggal</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="num">Total</th>
                    <th class="num">Terbayar</th>
                    <th class="num">Sisa</th>
                    <th class="num">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    @php
                        $displayStatus = $bill->isOpen() && $bill->due_date->isPast() && $bill->status !== 'overdue'
                            ? 'overdue'
                            : $bill->status;
                    @endphp
                    <tr>
                        <td>{{ $bill->number }}</td>
                        <td>{{ $bill->contact->name }}</td>
                        <td>{{ $bill->bill_date->format('d M Y') }}</td>
                        <td>{{ $bill->due_date->format('d M Y') }}</td>
                        <td><span class="tag {{ $displayStatus }}">{{ strtoupper($displayStatus) }}</span></td>
                        <td class="num">Rp {{ number_format($bill->total, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($bill->outstandingAmount(), 0, ',', '.') }}</td>
                        <td class="num">
                            @if ($user->canManage('purchases') && ($bill->isOpen() || $bill->status === 'draft'))
                                <form class="pay-form" method="POST" action="{{ route('purchases.pay', $bill) }}">
                                    @csrf
                                    <input class="js-rupiah" type="text" name="amount" placeholder="Bayar" required>
                                    <button class="button small" type="submit">Bayar</button>
                                </form>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">Belum ada tagihan pembelian. Buat tagihan pertama di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
