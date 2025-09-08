<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Http\Requests\UpdatesettingsRequest;

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
        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Successfully updated settings.'
        ]);
    }
}
