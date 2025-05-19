<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    /**
     * Display the admin settings page.
     */
    public function index(Request $request): View
    {
        $settings = \App\Models\Setting::getAllGrouped();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Store the updated settings.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $rules = [];
        $settingsToUpdate = $request->except('_token');

        foreach ($settingsToUpdate as $key => $value) {
            $settingModel = \App\Models\Setting::where('key', $key)->first();
            if ($settingModel) {
                // Basic validation based on type - can be expanded
                switch ($settingModel->type) {
                    case 'integer':
                        $rules[$key] = 'required|integer';
                        break;
                    case 'boolean':
                        $rules[$key] = 'required|boolean';
                        // Convert 'on'/'off' or '1'/'0' from checkbox to boolean for storage if needed
                        $settingsToUpdate[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'string':
                        if ($key === 'admin_notification_email') {
                            $rules[$key] = 'required|email|max:255';
                        } else {
                            $rules[$key] = 'required|string|max:255';
                        }
                        break;
                    case 'text':
                        $rules[$key] = 'required|string';
                        break;
                }
            }
        }

        $request->validate($rules);

        foreach ($settingsToUpdate as $key => $value) {
            \App\Models\Setting::where('key', $key)->update(['value' => $value]);
        }

        // Clear cache for all settings
        \Illuminate\Support\Facades\Cache::forget('app_settings_grouped');
        foreach (array_keys($settingsToUpdate) as $key) {
            \Illuminate\Support\Facades\Cache::forget('setting_' . $key);
        }


        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
