<x-filament-panels::page>
    @push('styles')
    <style>
        /* Fix spacing fields layout - prevent fields from going to new line */
        .fi-section-content-ctn .fi-section-content > div[class*="grid"] {
            display: grid !important;
        }
        
        /* Ensure Grid with 2 columns stays on same row */
        .fi-section-content-ctn .fi-section-content > div[class*="grid"][style*="grid-template-columns: repeat(2"] {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
        
        /* Prevent wrapping in legacy spacing fields */
        .fi-section-content-ctn input[name*="spacing"][name*="value"],
        .fi-section-content-ctn select[name*="spacing"][name*="unit"] {
            flex-shrink: 0;
        }
        
        /* Force grid layout for spacing fields */
        .fi-section-content-ctn .fi-section-content > div:has(input[name*="spacing_small_value"]) {
            display: grid !important;
            grid-template-columns: 1fr 200px !important;
            gap: 1rem !important;
        }
        
        .fi-section-content-ctn .fi-section-content > div:has(input[name*="spacing_medium_value"]) {
            display: grid !important;
            grid-template-columns: 1fr 200px !important;
            gap: 1rem !important;
        }
        
        .fi-section-content-ctn .fi-section-content > div:has(input[name*="spacing_large_value"]) {
            display: grid !important;
            grid-template-columns: 1fr 200px !important;
            gap: 1rem !important;
        }
    </style>
    @endpush

    <!-- Horizontal Tabs Menu -->
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'general'"
            wire:click="$set('activeTab', 'general')"
        >
            General
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'typography'"
            wire:click="$set('activeTab', 'typography')"
        >
            Typography
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'legacy'"
            wire:click="$set('activeTab', 'legacy')"
        >
            Legacy
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'si'"
            wire:click="$set('activeTab', 'si')"
        >
            S.I.
        </x-filament::tabs.item>
    </x-filament::tabs>

    <!-- Typography Language Sub-tabs (only visible when Typography tab is active) -->
    @if($activeTab === 'typography')
        <div class="mt-4 space-y-3">
            <!-- API Connection Status Indicator -->
            <div class="space-y-2">
                <div class="fi-section rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 {{ $googleFontsApiAvailable ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}" 
                     style="padding-top: 1rem; padding-bottom: 1rem; padding-left: 1.5rem; padding-right: 1.5rem;">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium {{ $googleFontsApiAvailable ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                                Google Fonts API: {{ $googleFontsApiAvailable ? 'Connected' : 'Unavailable' }}
                            </span>
                        </div>
                        <x-filament::button
                            wire:click="refreshGoogleFontsApiConnection"
                            size="sm"
                            color="primary"
                            icon="heroicon-o-arrow-path"
                            class="w-fit"
                        >
                            Check Connection
                        </x-filament::button>
                    </div>
                </div>
                
                @if(!$googleFontsApiAvailable && $googleFontsApiError)
                    <div class="fi-section rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10" 
                         style="padding-top: 0.75rem; padding-bottom: 0.75rem; padding-left: 1rem; padding-right: 1rem;">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            <strong>Error:</strong> {{ $googleFontsApiError }}
                        </p>
                        @php
                            $cachedFonts = Cache::get('google_fonts_list_fallback');
                        @endphp
                        @if(!empty($cachedFonts))
                            <p class="text-xs mt-1 text-gray-500 dark:text-gray-500">
                                Using {{ count($cachedFonts) }} cached fonts from previous successful connection.
                            </p>
                        @else
                            <p class="text-xs mt-1 text-gray-500 dark:text-gray-500">
                                No cached fonts available. Please check your internet connection and API key configuration.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <x-filament::tabs>
                <x-filament::tabs.item
                    :active="$typographyLang === 'en'"
                    wire:click="$set('typographyLang', 'en')"
                >
                    EN (English)
                </x-filament::tabs.item>
                <x-filament::tabs.item
                    :active="$typographyLang === 'gr'"
                    wire:click="$set('typographyLang', 'gr')"
                >
                    GR (Greek)
                </x-filament::tabs.item>
            </x-filament::tabs>
        </div>
    @endif

    <div class="mt-6">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit">
                    @if($activeTab === 'typography')
                        Save Typography ({{ strtoupper($typographyLang) }}) Variables
                    @else
                        Save {{ ucfirst($activeTab) }} Variables
                    @endif
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
