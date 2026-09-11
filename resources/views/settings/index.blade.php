@extends('layouts.app')
@section('title', 'Pengaturan Aplikasi')
@section('crumb') Pengaturan / <strong>Nama Aplikasi</strong> @endsection
@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Branding</div>
        <h1>Pengaturan Aplikasi</h1>
        <p>Ubah nama aplikasi, nama perusahaan, dan tagline yang tampil di sistem.</p>
    </div>
</div>

<section class="panel table-wrap">
    <div class="panel-head">
        <h2>Identitas Aplikasi</h2>
        <span class="muted">Saat ini: {{ $appName }}</span>
    </div>
    @if ($user->canManage('settings'))
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="muted" for="app_name">Nama Aplikasi</label>
                    <input id="app_name" name="app_name" value="{{ old('app_name', $appName) }}" required>
                </div>
                <div>
                    <label class="muted" for="company_name">Nama Perusahaan</label>
                    <input id="company_name" name="company_name" value="{{ old('company_name', $companyName) }}" required>
                </div>
                <div class="full">
                    <label class="muted" for="tagline">Tagline</label>
                    <input id="tagline" name="tagline" value="{{ old('tagline', $tagline) }}" required>
                </div>
                <div class="full form-actions">
                    <button class="button" type="submit">Simpan Pengaturan</button>
                </div>
            </div>
        </form>
    @else
        <table class="table">
            <tbody>
                <tr><td>Nama Aplikasi</td><td class="num">{{ $appName }}</td></tr>
                <tr><td>Nama Perusahaan</td><td class="num">{{ $companyName }}</td></tr>
                <tr><td>Tagline</td><td class="num">{{ $tagline }}</td></tr>
            </tbody>
        </table>
        <p class="muted">Hanya owner yang dapat mengubah pengaturan ini.</p>
    @endif
</section>
@endsection
