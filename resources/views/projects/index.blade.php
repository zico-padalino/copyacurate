@extends('layouts.app')
@section('title', 'Proyek')
@section('crumb') Keuangan / <strong>Proyek</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Project accounting</div><h1>Proyek</h1><p>Pantau anggaran, status, dan timeline proyek.</p></div>@if($user->canManage('projects'))<a class="button" href="#buat">+ Proyek Baru</a>@endif</div>
<div class="cards">
<div class="card"><div class="card-label">Aktif</div><div class="card-value">{{ $stats['active'] }}</div><span class="muted">Sedang berjalan</span></div>
<div class="card"><div class="card-label">Planning</div><div class="card-value">{{ $stats['planning'] }}</div><span class="muted">Persiapan</span></div>
<div class="card"><div class="card-label">Anggaran Berjalan</div><div class="card-value">Rp {{ number_format($stats['budget'], 0, ',', '.') }}</div><span class="muted">Planning + aktif</span></div>
<div class="card"><div class="card-label">Selesai</div><div class="card-value">{{ $stats['completed'] }}</div><span class="muted">Completed</span></div>
</div>
@if($user->canManage('projects'))
<section class="panel table-wrap" id="buat"><div class="panel-head"><h2>Tambah Proyek</h2></div>
<form method="POST" action="{{ route('projects.store') }}">@csrf
<div class="form-grid">
<div><label class="muted">Kode</label><input name="code" required></div>
<div><label class="muted">Nama</label><input name="name" required></div>
<div><label class="muted">Mulai</label><input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" required></div>
<div><label class="muted">Selesai</label><input type="date" name="end_date"></div>
<div><label class="muted">Anggaran</label><input class="js-rupiah" name="budget" value="0" required></div>
<div><label class="muted">Status</label><select name="status" required><option value="planning">Planning</option><option value="active">Active</option><option value="completed">Completed</option><option value="archived">Archived</option></select></div>
<div class="full form-actions"><button class="button" type="submit">Simpan</button></div>
</div></form></section>
@endif
<section class="panel table-wrap"><div class="panel-head"><h2>Daftar Proyek</h2></div>
<table class="table"><thead><tr><th>Kode</th><th>Nama</th><th>Mulai</th><th>Selesai</th><th class="num">Anggaran</th><th>Status</th><th class="num">Ubah Status</th></tr></thead>
<tbody>@forelse($projects as $project)<tr>
<td>{{ $project->code }}</td><td>{{ $project->name }}</td>
<td>{{ $project->start_date->format('d M Y') }}</td>
<td>{{ $project->end_date?->format('d M Y') ?: '—' }}</td>
<td class="num">Rp {{ number_format($project->budget, 0, ',', '.') }}</td>
<td><span class="tag {{ $project->status === 'active' ? 'sent' : ($project->status === 'completed' ? 'paid' : 'draft') }}">{{ strtoupper($project->status) }}</span></td>
<td class="num">@if($user->canManage('projects'))
<form class="pay-form" method="POST" action="{{ route('projects.status', $project) }}">@csrf
<select name="status"><option value="planning" @selected($project->status==='planning')>Planning</option><option value="active" @selected($project->status==='active')>Active</option><option value="completed" @selected($project->status==='completed')>Completed</option><option value="archived" @selected($project->status==='archived')>Archived</option></select>
<button class="button small" type="submit">Update</button></form>
@else<span class="muted">—</span>@endif</td>
</tr>@empty<tr><td colspan="7" class="empty">Belum ada proyek.</td></tr>@endforelse</tbody></table></section>
@endsection
