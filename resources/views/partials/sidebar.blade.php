<aside class="sidebar">
    <h1 class="logo">{{ $appName }}</h1>
    @php($authUser = auth()->user())
    <div class="nav-label">Workspace</div>
    <nav class="nav">
        @if($authUser?->canAccess('dashboard'))
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">◈ &nbsp; Ringkasan</a>
        @endif
        @if($authUser?->canAccess('journals'))
            <a class="{{ request()->routeIs('journals.*') ? 'active' : '' }}" href="{{ route('journals.index') }}">↗ &nbsp; Jurnal Umum</a>
        @endif
        @if($authUser?->canAccess('accounts'))
            <a class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">▤ &nbsp; Daftar Akun</a>
        @endif
        @if($authUser?->canAccess('reports'))
            <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">▥ &nbsp; Laporan Keuangan</a>
        @endif
    </nav>
    <div class="nav-label">Operasional</div>
    <nav class="nav">
        @if($authUser?->canAccess('sales'))
            <a class="{{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">▣ &nbsp; Penjualan & Piutang</a>
        @endif
        @if($authUser?->canAccess('purchases'))
            <a class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">▧ &nbsp; Pembelian & Utang</a>
        @endif
        @if($authUser?->canAccess('products'))
            <a class="{{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">□ &nbsp; Produk & Persediaan</a>
        @endif
        @if($authUser?->canAccess('contacts'))
            <a class="{{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">♙ &nbsp; Pelanggan & Vendor</a>
        @endif
    </nav>
    <div class="nav-label">Keuangan</div>
    <nav class="nav">
        @if($authUser?->canAccess('cash'))
            <a class="{{ request()->routeIs('cash.*') ? 'active' : '' }}" href="{{ route('cash.index') }}">▱ &nbsp; Kas & Bank</a>
        @endif
        @if($authUser?->canAccess('taxes'))
            <a class="{{ request()->routeIs('taxes.*') ? 'active' : '' }}" href="{{ route('taxes.index') }}">% &nbsp; Pajak</a>
        @endif
        @if($authUser?->canAccess('assets'))
            <a class="{{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">▥ &nbsp; Aset Tetap</a>
        @endif
        @if($authUser?->canAccess('projects'))
            <a class="{{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">⌂ &nbsp; Proyek</a>
        @endif
    </nav>
    <div class="nav-label">Pengaturan</div>
    <nav class="nav">
        @if($authUser?->canAccess('settings'))
            <a class="{{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">✎ &nbsp; Nama Aplikasi</a>
        @endif
        @if($authUser?->canManageRoles())
            <a class="{{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">☑ &nbsp; Hak Akses Role</a>
        @endif
        @if($authUser?->canAccess('users'))
            <a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">⚙ &nbsp; Pengguna & Role</a>
        @endif
        @if($authUser?->canAccess('integrations'))
            <a class="{{ request()->routeIs('integrations.*') ? 'active' : '' }}" href="{{ route('integrations.index') }}">⇄ &nbsp; Integrasi</a>
        @endif
    </nav>
</aside>
