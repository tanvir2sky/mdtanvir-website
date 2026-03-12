<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::query()->firstOrCreate(['id' => 1]);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'enable_gtm' => ['nullable', 'boolean'],
            'gtm_id' => ['nullable', 'string', 'max:50'],
            'enable_ga' => ['nullable', 'boolean'],
            'ga_measurement_id' => ['nullable', 'string', 'max:50'],
            'enable_clarity' => ['nullable', 'boolean'],
            'clarity_project_id' => ['nullable', 'string', 'max:50'],
        ]);

        $settings = SiteSetting::query()->firstOrCreate(['id' => 1]);
        $settings->update([
            'enable_gtm' => $request->boolean('enable_gtm'),
            'gtm_id' => trim((string) ($data['gtm_id'] ?? '')) ?: null,
            'enable_ga' => $request->boolean('enable_ga'),
            'ga_measurement_id' => trim((string) ($data['ga_measurement_id'] ?? '')) ?: null,
            'enable_clarity' => $request->boolean('enable_clarity'),
            'clarity_project_id' => trim((string) ($data['clarity_project_id'] ?? '')) ?: null,
        ]);

        return redirect()->route('admin.settings.edit')->with('status', 'Tracking settings updated.');
    }
}
