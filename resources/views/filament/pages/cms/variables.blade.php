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

    <div class="mt-6">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit">
                    Save {{ ucfirst($activeTab) }} Variables
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
