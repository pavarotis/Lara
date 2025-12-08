<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Models\Setting;
use App\Domain\Settings\Services\GetSettingsService;
use App\Domain\Settings\Services\UpdateSettingsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private GetSettingsService $getSettingsService,
        private UpdateSettingsService $updateSettingsService
    ) {}

    /**
     * Display the settings page
     */
    public function index(): View
    {
        $settings = $this->getSettingsService->all();
        $groups = Setting::select('group')
            ->distinct()
            ->whereNotNull('group')
            ->pluck('group')
            ->toArray();

        return view('admin.settings.index', [
            'settings' => $settings,
            'groups' => $groups,
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        // Get existing settings to preserve type and group
        $existingSettings = Setting::all()->keyBy('key');

        foreach ($validated['settings'] as $key => $value) {
            $setting = $existingSettings->get($key);
            if ($setting) {
                // Update existing setting
                $this->updateSettingsService->execute(
                    $key,
                    $value,
                    $setting->type,
                    $setting->group ?? 'general'
                );
            } else {
                // Create new setting (default to string type)
                $this->updateSettingsService->execute($key, $value, 'string', 'general');
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
