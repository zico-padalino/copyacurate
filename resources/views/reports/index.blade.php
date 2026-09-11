@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('crumb') Workspace / <strong>Laporan Keuangan</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Financial reports</div><h1>Laporan Keuangan</h1><p>Laba rugi, neraca, trial balance, dan aging piutang/utang.</p></div>
<a class="button ghost" href="{{ route('journals.index') }}">Ke Jurnal</a></div>
<div class="cards">
<div class="card"><div class="card-label">Pendapatan</div><div class="card-value">Rp {{ number_format($incomeStatement['revenue'], 0, ',', '.') }}</div><span class="muted">Kredit akun revenue</span></div>
<div class="card"><div class="card-label">Beban</div><div class="card-value">Rp {{ number_format($incomeStatement['expense'], 0, ',', '.') }}</div><span class="muted">Debit akun expense</span></div>
<div class="card"><div class="card-label">Laba Bersih</div><div class="card-value">Rp {{ number_format($incomeStatement['net_income'], 0, ',', '.') }}</div><span class="muted">Pendapatan − beban</span></div>
<div class="card"><div class="card-label">Total Aset (estimasi)</div><div class="card-value">Rp {{ number_format($balanceSheet['assets'], 0, ',', '.') }}</div><span class="muted">COA + kas + piutang + stok + aset</span></div>
</div>
<div class="grid">
<section class="panel"><div class="panel-head"><h2>Laba Rugi</h2><span class="muted">{{ now()->translatedFormat('F Y') }}</span></div>
<table class="table"><tbody>
<tr><td>Pendapatan</td><td class="num">Rp {{ number_format($incomeStatement['revenue'], 0, ',', '.') }}</td></tr>
<tr><td>Beban</td><td class="num">Rp {{ number_format($incomeStatement['expense'], 0, ',', '.') }}</td></tr>
<tr><td><strong>Laba Bersih</strong></td><td class="num"><strong>Rp {{ number_format($incomeStatement['net_income'], 0, ',', '.') }}</strong></td></tr>
</tbody></table></section>
<section class="panel"><div class="panel-head"><h2>Neraca Ringkas</h2></div>
<table class="table"><tbody>
<tr><td>Aset</td><td class="num">Rp {{ number_format($balanceSheet['assets'], 0, ',', '.') }}</td></tr>
<tr><td>Liabilitas</td><td class="num">Rp {{ number_format($balanceSheet['liabilities'], 0, ',', '.') }}</td></tr>
<tr><td>Ekuitas (+ laba)</td><td class="num">Rp {{ number_format($balanceSheet['equity'], 0, ',', '.') }}</td></tr>
</tbody></table></section>
</div>
<div class="grid">
<section class="panel"><div class="panel-head"><h2>Aging Piutang</h2><a class="panel-link" href="{{ route('sales.index') }}">Detail →</a></div>
<table class="table"><tbody>
<tr><td>Belum jatuh tempo</td><td class="num">Rp {{ number_format($receivableAging['current'], 0, ',', '.') }}</td></tr>
<tr><td>Overdue</td><td class="num">Rp {{ number_format($receivableAging['overdue'], 0, ',', '.') }}</td></tr>
</tbody></table></section>
<section class="panel"><div class="panel-head"><h2>Aging Utang</h2><a class="panel-link" href="{{ route('purchases.index') }}">Detail →</a></div>
<table class="table"><tbody>
<tr><td>Belum jatuh tempo</td><td class="num">Rp {{ number_format($payableAging['current'], 0, ',', '.') }}</td></tr>
<tr><td>Overdue</td><td class="num">Rp {{ number_format($payableAging['overdue'], 0, ',', '.') }}</td></tr>
</tbody></table></section>
</div>
<section class="panel table-wrap"><div class="panel-head"><h2>Trial Balance</h2></div>
<table class="table"><thead><tr><th>Kode</th><th>Akun</th><th>Tipe</th><th class="num">Saldo</th></tr></thead>
<tbody>@foreach($trialBalance as $row)<tr>
<td>{{ $row['code'] }}</td><td>{{ $row['name'] }}</td><td><span class="tag">{{ $row['type'] }}</span></td>
<td class="num">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
</tr>@endforeach</tbody></table></section>
@endsection
