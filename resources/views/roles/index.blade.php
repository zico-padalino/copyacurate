@extends('layouts.app')
@section('title', 'Hak Akses Role')
@section('crumb') Pengaturan / <strong>Hak Akses Role</strong> @endsection
@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Permission matrix</div>
        <h1>Hak Akses Role</h1>
        <p>Centang modul yang boleh diakses tiap role. <strong>Lihat</strong> = buka halaman, <strong>Kelola</strong> = tambah/ubah data.</p>
    </div>
    <form method="POST" action="{{ route('roles.reset') }}" onsubmit="return confirm('Kembalikan semua role ke default?')">
        @csrf
        <button class="button ghost" type="submit">Reset Default</button>
    </form>
</div>

<form method="POST" action="{{ route('roles.update') }}">
    @csrf
    <section class="panel table-wrap">
        <div class="panel-head">
            <h2>Matriks Izin</h2>
            <button class="button" type="submit">Simpan Hak Akses</button>
        </div>
        <div class="table-wrap" style="margin-top:0;overflow:auto">
            <table class="table permission-table">
                <thead>
                    <tr>
                        <th>Modul / Izin</th>
                        @foreach ($roles as $role)
                            <th class="num">{{ \App\Support\PermissionCatalog::roleLabel($role) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grouped as $group => $items)
                        <tr class="permission-group"><td colspan="{{ count($roles) + 1 }}"><strong>{{ $group }}</strong></td></tr>
                        @foreach ($items as $key => $meta)
                            <tr>
                                <td>
                                    <div>{{ $meta['label'] }}</div>
                                    <div class="muted">{{ $meta['description'] }}</div>
                                </td>
                                @foreach ($roles as $role)
                                    @php
                                        $locked = $role === 'owner' && in_array($key, \App\Support\PermissionCatalog::protectedOwnerPermissions(), true);
                                        $checked = $locked || (bool) ($matrix[$role][$key] ?? false);
                                    @endphp
                                    <td class="num">
                                        <label class="permission-check">
                                            <input
                                                type="checkbox"
                                                name="permissions[{{ $role }}][{{ $key }}]"
                                                value="1"
                                                @checked($checked)
                                                @disabled($locked)
                                            >
                                            @if ($locked)
                                                <input type="hidden" name="permissions[{{ $role }}][{{ $key }}]" value="1">
                                            @endif
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</form>
@endsection
