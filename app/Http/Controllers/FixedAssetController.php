<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\FixedAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixedAssetController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $assets = FixedAsset::query()->latest('id')->get();
        $stats = [
            'active' => $assets->where('status', 'active')->count(),
            'acquisition' => (float) $assets->where('status', 'active')->sum('acquisition_cost'),
            'book_value' => (float) $assets->where('status', 'active')->sum(fn (FixedAsset $asset) => $asset->bookValue()),
            'depreciation' => (float) $assets->sum('accumulated_depreciation'),
        ];

        return view('assets.index', compact('user', 'assets', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('assets'), 403);
        $request->merge(['acquisition_cost' => $this->sanitizeMoney($request->input('acquisition_cost'))]);

        $data = $request->validate([
            'asset_code' => ['required', 'string', 'max:40', 'unique:fixed_assets,asset_code'],
            'name' => ['required', 'string', 'max:160'],
            'acquired_at' => ['required', 'date'],
            'acquisition_cost' => ['required', 'numeric', 'min:1'],
            'useful_life_months' => ['required', 'integer', 'min:1'],
        ]);

        $data['accumulated_depreciation'] = 0;
        $data['status'] = 'active';
        FixedAsset::query()->create($data);

        return redirect()->route('assets.index')->with('success', 'Aset tetap berhasil ditambahkan.');
    }

    public function depreciate(FixedAsset $fixedAsset): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('assets'), 403);
        abort_unless($fixedAsset->status === 'active', 422);
        $fixedAsset->postMonthlyDepreciation();

        return redirect()->route('assets.index')->with('success', 'Penyusutan bulanan '.$fixedAsset->asset_code.' dicatat.');
    }

    public function dispose(FixedAsset $fixedAsset): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('assets'), 403);
        $fixedAsset->dispose();

        return redirect()->route('assets.index')->with('success', 'Aset '.$fixedAsset->asset_code.' ditandai disposed.');
    }
}
