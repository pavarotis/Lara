<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content">
                    {{ $this->form }}
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <x-filament::button
                type="button"
                wire:click="save"
                color="primary"
                size="lg"
            >
                Save All Settings
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
