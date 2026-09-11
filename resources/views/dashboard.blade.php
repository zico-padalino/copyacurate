@extends('layouts.app')

@section('title', 'Dashboard Keuangan')
@section('crumb')
    Workspace / <strong>Ringkasan</strong>
@endsection

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Selamat datang kembali</div>
        <h1>Ringkasan Keuangan</h1>
        <p>{{ now()->translatedFormat('l, d F Y') }} · {{ $companyName }}</p>
    </div>
</div>

<div class="cards">
    <div class="card">
        <div class="card-label">Total Pendapatan</div>
        <div class="card-value">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
        <span class="muted">Dari jurnal kredit pendapatan</span>
    </div>
    <div class="card">
        <div class="card-label">Total Pengeluaran</div>
        <div class="card-value">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        <span class="muted">Dari jurnal debit beban</span>
    </div>
    <div class="card">
        <div class="card-label">Laba Bersih</div>
        <div class="card-value">Rp {{ number_format($revenue - $expense, 0, ',', '.') }}</div>
        <span class="muted">Pendapatan − beban</span>
    </div>
    <div class="card">
        <div class="card-label">Saldo Kas & Bank</div>
        <div class="card-value">Rp {{ number_format($moduleStats['cash'], 0, ',', '.') }}</div>
        <span class="muted">Rekening kas aktif</span>
    </div>
</div>

<div class="cards module-cards">
    <div class="card">
        <div class="card-label">Pelanggan & Vendor</div>
        <div class="card-value">{{ $moduleStats['contacts'] }}</div>
        <span class="muted">Master kontak aktif</span>
    </div>
    <div class="card">
        <div class="card-label">Piutang Berjalan</div>
        <div class="card-value">Rp {{ number_format($moduleStats['receivables'], 0, ',', '.') }}</div>
        <span class="muted"><a class="panel-link" href="{{ route('sales.index') }}">Kelola piutang →</a></span>
    </div>
    <div class="card">
        <div class="card-label">Utang Berjalan</div>
        <div class="card-value">Rp {{ number_format($moduleStats['payables'], 0, ',', '.') }}</div>
        <span class="muted"><a class="panel-link" href="{{ route('purchases.index') }}">Kelola utang →</a></span>
    </div>
    <div class="card">
        <div class="card-label">Stok Menipis</div>
        <div class="card-value">{{ $moduleStats['lowStock'] }}</div>
        <span class="muted"><a class="panel-link" href="{{ route('products.index') }}">Kelola produk →</a></span>
    </div>
</div>

<div class="grid">
    <section class="panel">
        <div class="panel-head">
            <h2>Arus Keuangan</h2>
            <a class="panel-link" href="{{ route('journals.index') }}">Lihat laporan →</a>
        </div>
        <div class="bars">
            @foreach ([55, 68, 46, 79, 72, 91, 84, 62, 76, 88, 94, 100] as $height)
                <div class="bar-col">
                    <div class="bar {{ $loop->last ? 'highlight' : '' }}" style="height:{{ $height }}%"></div>
                    <span class="month">{{ ['Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'][$loop->index] }}</span>
                </div>
            @endforeach
        </div>
    </section>
    <section class="panel">
        <div class="panel-head">
            <h2>Komposisi Aset</h2>
            <span class="muted">{{ now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="donut-row">
            <div class="donut"></div>
            <div class="legend">
                <div><i class="dot"></i> Kas & Bank <b>42%</b></div>
                <div><i class="dot gold"></i> Piutang <b>26%</b></div>
                <div><i class="dot light"></i> Persediaan <b>16%</b></div>
                <div><i class="dot" style="background:#dce9df"></i> Aset tetap <b>16%</b></div>
            </div>
        </div>
    </section>
</div>

@if ($user->canManage('journals'))
<section class="panel table-wrap" id="transaksi">
    <div class="panel-head">
        <h2>Catat Jurnal Umum</h2>
        <span class="muted">Debit dan kredit harus seimbang</span>
    </div>
    <form class="journal-form" method="POST" action="{{ route('journal.store') }}">
        @csrf
        <input type="date" name="entry_date" value="{{ now()->format('Y-m-d') }}" required>
        <input name="description" placeholder="Deskripsi transaksi" required>
        <input class="js-rupiah" type="text" name="amount" placeholder="Nominal" required>
        <select name="debit_account_id" required>
            <option value="">Debit...</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
            @endforeach
        </select>
        <select name="credit_account_id" required>
            <option value="">Kredit...</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
            @endforeach
        </select>
        <button class="button" type="submit">Simpan</button>
    </form>
</section>
@endif

<section class="panel table-wrap" id="laporan">
    <div class="panel-head">
        <h2>Transaksi Terbaru</h2>
        <a class="panel-link" href="{{ route('reports.index') }}">Lihat semua →</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Referensi</th>
                <th>Deskripsi</th>
                <th>Akun</th>
                <th>Status</th>
                <th class="num">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                    <td>{{ $entry->reference }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->debitAccount->name }} → {{ $entry->creditAccount->name }}</td>
                    <td><span class="tag">Posted</span></td>
                    <td class="num">Rp {{ number_format($entry->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Belum ada transaksi jurnal.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
