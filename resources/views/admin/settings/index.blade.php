<x-admin-layout>
    <x-slot name="title">Settings</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">System Settings</h2>
            <p class="text-gray-600">Manage global system settings.</p>
        </div>

        <!-- Settings Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                @foreach($groups as $group)
                    <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 capitalize">{{ str_replace('_', ' ', $group) }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $groupSettings = \App\Domain\Settings\Models\Setting::group($group)->get();
                            @endphp
                            @foreach($groupSettings as $setting)
                                <div>
                                    <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    @if($setting->type === 'boolean')
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="settings[{{ $setting->key }}]" 
                                                id="settings[{{ $setting->key }}]"
                                                value="1"
                                                {{ ($settings[$setting->key] ?? false) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary focus:ring-primary">
                                            <label for="settings[{{ $setting->key }}]" class="ml-2 text-sm text-gray-600">
                                                {{ ($settings[$setting->key] ?? false) ? 'Enabled' : 'Disabled' }}
                                            </label>
                                        </div>
                                    @elseif($setting->type === 'json')
                                        <textarea 
                                            name="settings[{{ $setting->key }}]" 
                                            id="settings[{{ $setting->key }}]"
                                            rows="4"
                                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">{{ is_array($settings[$setting->key] ?? null) ? json_encode($settings[$setting->key], JSON_PRETTY_PRINT) : ($settings[$setting->key] ?? '') }}</textarea>
                                    @else
                                        <input 
                                            type="{{ $setting->type === 'integer' || $setting->type === 'decimal' ? 'number' : 'text' }}"
                                            name="settings[{{ $setting->key }}]" 
                                            id="settings[{{ $setting->key }}]"
                                            value="{{ $settings[$setting->key] ?? '' }}"
                                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                    @endif
                                    @if($setting->description ?? null)
                                        <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Settings without group -->
                @php
                    $ungroupedSettings = \App\Domain\Settings\Models\Setting::whereNull('group')->get();
                @endphp
                @if($ungroupedSettings->count() > 0)
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">General</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($ungroupedSettings as $setting)
                                <div>
                                    <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    <input 
                                        type="text"
                                        name="settings[{{ $setting->key }}]" 
                                        id="settings[{{ $setting->key }}]"
                                        value="{{ $settings[$setting->key] ?? '' }}"
                                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

