<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClinicSettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $settings = ClinicSetting::where('clinic_id', $request->user()->clinic_id)
            ->get()
            ->groupBy('group')
            ->map(fn ($group) => $group->pluck('value', 'key'));

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        $clinicId = $request->user()->clinic_id;

        foreach ($request->settings as $setting) {
            ClinicSetting::where('clinic_id', $clinicId)
                ->where('key', $setting['key'])
                ->update([
                    'value' => $setting['value'],
                    'updated_at' => now(),
                ]);
        }

        // Return fresh settings
        $settings = ClinicSetting::where('clinic_id', $clinicId)
            ->get()
            ->groupBy('group')
            ->map(fn ($group) => $group->pluck('value', 'key'));

        return response()->json($settings);
    }
}
