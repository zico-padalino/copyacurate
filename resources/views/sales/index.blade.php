@extends('layouts.app')

@section('title', 'Penjualan & Piutang')
@section('crumb')
    Operasional / <strong>Penjualan & Piutang</strong>
@endsection

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Modul piutang</div>
        <h1>Penjualan & Piutang</h1>
        <p>Kelola faktur pelanggan, jatuh tempo, dan penerimaan pembayaran.</p>
    </div>
    @if ($user->canManage('sales'))
        <a class="button" href="#buat-faktur">+ Faktur Baru</a>
    @endif
</div>

<div class="cards">
    <div class="card">
        <div class="card-label">Faktur Berjalan</div>
        <div class="card-value">{{ $stats['open_count'] }}</div>
        <span class="muted">Belum lunas</span>
    </div>
    <div class="card">
        <div class="card-label">Piutang Outstanding</div>
        <div class="card-value">Rp {{ number_format($stats['outstanding'], 0, ',', '.') }}</div>
        <span class="muted">Total − terbayar</span>
    </div>
    <div class="card">
        <div class="card-label">Jatuh Tempo / Overdue</div>
        <div class="card-value">Rp {{ number_format($stats['overdue'], 0, ',', '.') }}</div>
        <span class="muted">Perlu ditagih</span>
    </div>
    <div class="card">
        <div class="card-label">Sudah Diterima</div>
        <div class="card-value">Rp {{ number_format($stats['collected'], 0, ',', '.') }}</div>
        <span class="muted">Akumulasi pembayaran</span>
    </div>
</div>

@if ($user->canManage('sales'))
<section class="panel table-wrap" id="buat-faktur">
    <div class="panel-head">
        <h2>Buat Faktur Penjualan</h2>
        <span class="muted">Otomatis hitung PPN bila dipilih</span>
    </div>
    <form method="POST" action="{{ route('sales.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <label class="muted" for="contact_id">Pelanggan</label>
                <select id="contact_id" name="contact_id" required>
                    <option value="">Pilih pelanggan...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('contact_id') == $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="muted" for="invoice_date">Tanggal Faktur</label>
                <input id="invoice_date" type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="muted" for="due_date">Jatuh Tempo</label>
                <input id="due_date" type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="muted" for="subtotal">Subtotal (Rp)</label>
                <input id="subtotal" class="js-rupiah" type="text" name="subtotal" value="{{ old('subtotal') }}" placeholder="Contoh: 10.000.000" required>
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
                    <option value="sent" @selected(old('status', 'sent') === 'sent')>Kirim / Piutang</option>
                    <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                </select>
            </div>
            <div class="full form-actions">
                <button class="button" type="submit">Simpan Faktur</button>
            </div>
        </div>
    </form>
</section>
@endif

<section class="panel table-wrap">
    <div class="panel-head">
        <h2>Daftar Faktur</h2>
        <a class="panel-link" href="{{ route('purchases.index') }}">Lihat utang →</a>
    </div>
    <div class="table-wrap" style="margin-top:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Pelanggan</th>
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
                @forelse ($invoices as $invoice)
                    @php
                        $displayStatus = $invoice->isOpen() && $invoice->due_date->isPast() && $invoice->status !== 'overdue'
                            ? 'overdue'
                            : $invoice->status;
                    @endphp
                    <tr>
                        <td>{{ $invoice->number }}</td>
                        <td>{{ $invoice->contact->name }}</td>
                        <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td>{{ $invoice->due_date->format('d M Y') }}</td>
                        <td><span class="tag {{ $displayStatus }}">{{ strtoupper($displayStatus) }}</span></td>
                        <td class="num">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($invoice->outstandingAmount(), 0, ',', '.') }}</td>
                        <td class="num">
                            @if ($user->canManage('sales') && ($invoice->isOpen() || $invoice->status === 'draft'))
                                <form class="pay-form" method="POST" action="{{ route('sales.pay', $invoice) }}">
                                    @csrf
                                    <input class="js-rupiah" type="text" name="amount" placeholder="Bayar" required>
                                    <button class="button small" type="submit">Terima</button>
                                </form>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">Belum ada faktur penjualan. Buat faktur pertama di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
