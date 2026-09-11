@extends('layouts.app')
@section('title', 'Integrasi')
@section('crumb') Pengaturan / <strong>Integrasi</strong> @endsection
@section('content')
<div class="heading"><div><div class="eyebrow">Konektivitas</div><h1>Integrasi</h1><p>Hubungkan payment gateway, e-Faktur, WhatsApp, dan sinkronisasi akuntansi.</p></div></div>
<div class="cards">
<div class="card"><div class="card-label">Terhubung</div><div class="card-value">{{ $stats['connected'] }}</div><span class="muted">dari {{ $stats['available'] }}</span></div>
<div class="card"><div class="card-label">Payment Gateway</div><div class="card-value">{{ $stats['payments'] }}</div><span class="muted">Xendit / Midtrans</span></div>
<div class="card"><div class="card-label">e-Faktur</div><div class="card-value">{{ $stats['tax'] ? 'ON' : 'OFF' }}</div><span class="muted">DJP</span></div>
<div class="card"><div class="card-label">Tersedia</div><div class="card-value">{{ $stats['available'] }}</div><span class="muted">Katalog integrasi</span></div>
</div>
<form method="POST" action="{{ route('integrations.update') }}">@csrf
<section class="panel table-wrap"><div class="panel-head"><h2>Katalog Integrasi</h2>@if($user->canManage('integrations'))<button class="button" type="submit">Simpan Pengaturan</button>@endif</div>
<table class="table"><thead><tr><th>Layanan</th><th>Deskripsi</th><th>API Key</th><th>Status</th></tr></thead>
<tbody>@foreach($integrations as $integration)<tr>
<td><strong>{{ $integration['label'] }}</strong></td>
<td>{{ $integration['description'] }}</td>
<td>@if($user->canManage('integrations'))
<input name="integrations[{{ $integration['key'] }}][api_key]" value="{{ $integration['api_key'] }}" placeholder="API key / token">
@else<span class="muted">{{ $integration['api_key'] ? '••••••••' : '—' }}</span>@endif</td>
<td>@if($user->canManage('integrations'))
<label class="muted"><input type="checkbox" name="integrations[{{ $integration['key'] }}][enabled]" value="1" @checked($integration['enabled'])> Aktif</label>
@else<span class="tag {{ $integration['enabled'] ? 'sent' : 'draft' }}">{{ $integration['enabled'] ? 'ON' : 'OFF' }}</span>@endif</td>
</tr>@endforeach</tbody></table></section>
</form>
@endsection
