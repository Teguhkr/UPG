<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $inputs = $request->input('settings', []);
        foreach ($inputs as $key => $value) {
            SiteSetting::where('key', $key)->update(['value' => $value]);
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan situs berhasil disimpan.');
    }
}

