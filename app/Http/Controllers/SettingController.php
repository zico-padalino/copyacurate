<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Support\AppSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->currentUser();

        return view('settings.index', [
            'user' => $user,
            'appName' => AppSettings::appName(),
            'companyName' => AppSettings::companyName(),
            'tagline' => AppSettings::tagline(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($this->currentUser()->canManage('settings'), 403, 'Hanya owner yang dapat mengubah pengaturan aplikasi.');

        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:80'],
            'company_name' => ['required', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:160'],
        ]);

        AppSettings::update($data);

        return redirect()->route('settings.index')->with('success', 'Pengaturan aplikasi berhasil disimpan.');
    }
}
