<?php

namespace App\Support;

use App\Models\RolePermission;

class PermissionCatalog
{
    /**
     * @return array<string, array{label: string, group: string, description: string, module: string, action: string}>
     */
    public static function all(): array
    {
        return [
            'dashboard_view' => ['label' => 'Ringkasan', 'group' => 'Workspace', 'description' => 'Lihat dashboard', 'module' => 'dashboard', 'action' => 'view'],
            'journals_view' => ['label' => 'Jurnal Umum (lihat)', 'group' => 'Workspace', 'description' => 'Buka halaman jurnal', 'module' => 'journals', 'action' => 'view'],
            'journals_manage' => ['label' => 'Jurnal Umum (kelola)', 'group' => 'Workspace', 'description' => 'Catat jurnal baru', 'module' => 'journals', 'action' => 'manage'],
            'accounts_view' => ['label' => 'Daftar Akun (lihat)', 'group' => 'Workspace', 'description' => 'Buka chart of accounts', 'module' => 'accounts', 'action' => 'view'],
            'accounts_manage' => ['label' => 'Daftar Akun (kelola)', 'group' => 'Workspace', 'description' => 'Tambah akun', 'module' => 'accounts', 'action' => 'manage'],
            'reports_view' => ['label' => 'Laporan Keuangan', 'group' => 'Workspace', 'description' => 'Lihat laporan', 'module' => 'reports', 'action' => 'view'],
            'sales_view' => ['label' => 'Penjualan & Piutang (lihat)', 'group' => 'Operasional', 'description' => 'Buka modul penjualan', 'module' => 'sales', 'action' => 'view'],
            'sales_manage' => ['label' => 'Penjualan & Piutang (kelola)', 'group' => 'Operasional', 'description' => 'Buat faktur & terima bayar', 'module' => 'sales', 'action' => 'manage'],
            'purchases_view' => ['label' => 'Pembelian & Utang (lihat)', 'group' => 'Operasional', 'description' => 'Buka modul pembelian', 'module' => 'purchases', 'action' => 'view'],
            'purchases_manage' => ['label' => 'Pembelian & Utang (kelola)', 'group' => 'Operasional', 'description' => 'Buat tagihan & bayar utang', 'module' => 'purchases', 'action' => 'manage'],
            'products_view' => ['label' => 'Produk (lihat)', 'group' => 'Operasional', 'description' => 'Buka persediaan', 'module' => 'products', 'action' => 'view'],
            'products_manage' => ['label' => 'Produk (kelola)', 'group' => 'Operasional', 'description' => 'Tambah produk & stok', 'module' => 'products', 'action' => 'manage'],
            'contacts_view' => ['label' => 'Kontak (lihat)', 'group' => 'Operasional', 'description' => 'Buka pelanggan/vendor', 'module' => 'contacts', 'action' => 'view'],
            'contacts_manage' => ['label' => 'Kontak (kelola)', 'group' => 'Operasional', 'description' => 'Tambah/ubah kontak', 'module' => 'contacts', 'action' => 'manage'],
            'cash_view' => ['label' => 'Kas & Bank (lihat)', 'group' => 'Keuangan', 'description' => 'Buka rekening kas', 'module' => 'cash', 'action' => 'view'],
            'cash_manage' => ['label' => 'Kas & Bank (kelola)', 'group' => 'Keuangan', 'description' => 'Transfer & sesuaikan saldo', 'module' => 'cash', 'action' => 'manage'],
            'taxes_view' => ['label' => 'Pajak (lihat)', 'group' => 'Keuangan', 'description' => 'Buka modul pajak', 'module' => 'taxes', 'action' => 'view'],
            'taxes_manage' => ['label' => 'Pajak (kelola)', 'group' => 'Keuangan', 'description' => 'Kelola tarif pajak', 'module' => 'taxes', 'action' => 'manage'],
            'assets_view' => ['label' => 'Aset Tetap (lihat)', 'group' => 'Keuangan', 'description' => 'Buka aset tetap', 'module' => 'assets', 'action' => 'view'],
            'assets_manage' => ['label' => 'Aset Tetap (kelola)', 'group' => 'Keuangan', 'description' => 'Tambah aset & penyusutan', 'module' => 'assets', 'action' => 'manage'],
            'projects_view' => ['label' => 'Proyek (lihat)', 'group' => 'Keuangan', 'description' => 'Buka proyek', 'module' => 'projects', 'action' => 'view'],
            'projects_manage' => ['label' => 'Proyek (kelola)', 'group' => 'Keuangan', 'description' => 'Tambah & ubah status proyek', 'module' => 'projects', 'action' => 'manage'],
            'settings_view' => ['label' => 'Nama Aplikasi (lihat)', 'group' => 'Pengaturan', 'description' => 'Buka pengaturan branding', 'module' => 'settings', 'action' => 'view'],
            'settings_manage' => ['label' => 'Nama Aplikasi (kelola)', 'group' => 'Pengaturan', 'description' => 'Ubah nama aplikasi', 'module' => 'settings', 'action' => 'manage'],
            'users_view' => ['label' => 'Pengguna (lihat)', 'group' => 'Pengaturan', 'description' => 'Buka daftar pengguna', 'module' => 'users', 'action' => 'view'],
            'users_manage' => ['label' => 'Pengguna (kelola)', 'group' => 'Pengaturan', 'description' => 'Tambah user & ubah role', 'module' => 'users', 'action' => 'manage'],
            'integrations_view' => ['label' => 'Integrasi (lihat)', 'group' => 'Pengaturan', 'description' => 'Buka integrasi', 'module' => 'integrations', 'action' => 'view'],
            'integrations_manage' => ['label' => 'Integrasi (kelola)', 'group' => 'Pengaturan', 'description' => 'Ubah koneksi integrasi', 'module' => 'integrations', 'action' => 'manage'],
            'roles_manage' => ['label' => 'Hak Akses Role', 'group' => 'Pengaturan', 'description' => 'Atur permission tiap role', 'module' => 'roles', 'action' => 'manage'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return list<string>
     */
    public static function roles(): array
    {
        return ['owner', 'accountant', 'viewer'];
    }

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'owner' => 'Owner',
            'accountant' => 'Akuntan',
            'viewer' => 'Viewer',
            default => $role,
        };
    }

    public static function key(string $module, string $action): string
    {
        return $module.'_'.$action;
    }

    /**
     * @return array<string, bool>
     */
    public static function defaultsFor(string $role): array
    {
        $defaults = array_fill_keys(self::keys(), false);

        if ($role === 'owner') {
            return array_fill_keys(self::keys(), true);
        }

        if ($role === 'accountant') {
            foreach (self::keys() as $key) {
                $defaults[$key] = ! in_array($key, ['users_manage', 'settings_manage', 'integrations_manage', 'roles_manage'], true);
            }
            $defaults['users_view'] = true;
            $defaults['settings_view'] = true;
            $defaults['integrations_view'] = true;

            return $defaults;
        }

        foreach (self::keys() as $key) {
            $defaults[$key] = str_ends_with($key, '_view');
        }

        return $defaults;
    }

    public static function syncDefaults(): void
    {
        foreach (self::roles() as $role) {
            foreach (self::defaultsFor($role) as $permission => $allowed) {
                RolePermission::query()->updateOrCreate(
                    ['role' => $role, 'permission' => $permission],
                    ['allowed' => $allowed],
                );
            }
            RolePermission::forgetRoleCache($role);
        }
    }

    /**
     * @return list<string>
     */
    public static function protectedOwnerPermissions(): array
    {
        return ['dashboard_view', 'users_view', 'users_manage', 'roles_manage'];
    }
}
