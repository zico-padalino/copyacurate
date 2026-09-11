<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    use ResolvesDemoUser;

    /** @var array<string, array{label: string, description: string}> */
    private array $catalog = [
        'mailpit' => [
            'label' => 'Mailpit',
            'description' => 'Inbox email lokal untuk notifikasi invoice dan reminder piutang.',
        ],
        'xendit' => [
            'label' => 'Xendit',
            'description' => 'Payment gateway untuk penerimaan pembayaran pelanggan.',
        ],
        'midtrans' => [
            'label' => 'Midtrans',
            'description' => 'Alternatif payment gateway QRIS & virtual account.',
        ],
        'efaktur' => [
            'label' => 'e-Faktur DJP',
            'description' => 'Sinkronisasi Faktur Pajak Keluaran/Masukan.',
        ],
        'whatsapp' => [
            'label' => 'WhatsApp Business',
            'description' => 'Kirim pengingat jatuh tempo via WhatsApp.',
        ],
        'accurate' => [
            'label' => 'Accurate Online',
            'description' => 'Impor/ekspor chart of accounts dan jurnal.',
        ],
    ];

    public function index(): View
    {
        $user = $this->demoUser();
        $settings = Cache::get('ledger.integrations', [
            'mailpit' => ['enabled' => true, 'api_key' => 'local-mailpit'],
            'xendit' => ['enabled' => false, 'api_key' => ''],
            'midtrans' => ['enabled' => false, 'api_key' => ''],
            'efaktur' => ['enabled' => false, 'api_key' => ''],
            'whatsapp' => ['enabled' => false, 'api_key' => ''],
            'accurate' => ['enabled' => false, 'api_key' => ''],
        ]);

        $integrations = collect($this->catalog)->map(function (array $meta, string $key) use ($settings) {
            return [
                'key' => $key,
                'label' => $meta['label'],
                'description' => $meta['description'],
                'enabled' => (bool) ($settings[$key]['enabled'] ?? false),
                'api_key' => (string) ($settings[$key]['api_key'] ?? ''),
            ];
        })->values();

        $stats = [
            'connected' => $integrations->where('enabled', true)->count(),
            'available' => $integrations->count(),
            'payments' => $integrations->whereIn('key', ['xendit', 'midtrans'])->where('enabled', true)->count(),
            'tax' => $integrations->firstWhere('key', 'efaktur')['enabled'] ? 1 : 0,
        ];

        return view('integrations.index', compact('user', 'integrations', 'stats'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('integrations'), 403);

        $data = $request->validate([
            'integrations' => ['required', 'array'],
            'integrations.*.enabled' => ['nullable', 'boolean'],
            'integrations.*.api_key' => ['nullable', 'string', 'max:120'],
        ]);

        $settings = [];
        foreach (array_keys($this->catalog) as $key) {
            $settings[$key] = [
                'enabled' => (bool) data_get($data, "integrations.$key.enabled", false),
                'api_key' => (string) data_get($data, "integrations.$key.api_key", ''),
            ];
        }

        Cache::forever('ledger.integrations', $settings);

        return redirect()->route('integrations.index')->with('success', 'Pengaturan integrasi disimpan.');
    }
}
