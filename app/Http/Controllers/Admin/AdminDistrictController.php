<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDistrictController extends Controller
{
    /**
     * Show the district coverage settings form.
     */
    public function edit(): View
    {
        $hierarchy = SystemSetting::getAllPeruCoverageHierarchy();
        $activeDistricts = SystemSetting::getActiveDistricts();

        return view('admin.settings.districts', compact('hierarchy', 'activeDistricts'));
    }

    /**
     * Update the district coverage settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $hierarchy = SystemSetting::getAllPeruCoverageHierarchy();
        $validDistricts = [];
        foreach ($hierarchy as $dept => $provinces) {
            foreach ($provinces as $prov => $districts) {
                foreach ($districts as $dist) {
                    $validDistricts[] = "{$dept}|{$prov}|{$dist}";
                }
            }
        }

        $request->validate([
            'districts' => ['nullable', 'array'],
            'districts.*' => ['string', 'in:'.implode(',', $validDistricts)],
        ]);

        $active = $request->input('districts', []);

        SystemSetting::updateOrCreate(
            ['key' => 'active_districts'],
            [
                'value' => json_encode(array_values($active)),
                'description' => 'Listado JSON de distritos activos de Perú para cobertura de Nikama.',
            ]
        );

        return redirect()->route('admin.settings.districts.edit')
            ->with('success', 'Zonas de cobertura actualizadas correctamente.');
    }
}
