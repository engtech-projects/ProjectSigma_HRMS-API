<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Http\Requests\UpdatesettingsRequest;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Settings::all(),
            'message' => 'Successfully fetched all settings.'
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Settings $setting, UpdatesettingsRequest $request)
    {
        $validatedData = $request->validated();
        $setting->value = $validatedData['value'];
        $setting->save();
        Cache::forget('settings_'.$setting->setting_name);
        Cache::rememberForever('settings_'.$setting->setting_name, function () use ($setting) {
            return $setting->value;
        });
        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Successfully updated settings.'
        ]);
    }
}
