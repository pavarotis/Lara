<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::section>
            <x-slot name="heading">
                Current Footer Variant
            </x-slot>
            <x-slot name="description">
                Preview of the current footer variant configuration
            </x-slot>

            @php
                $variantService = app(\App\Domain\Themes\Services\GetFooterVariantService::class);
                $currentVariant = $this->business ? $variantService->getVariant($this->business) : null;
            @endphp

            @if($currentVariant)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Variant Name:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $currentVariant['name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Layout:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $currentVariant['layout'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Show Opening Hours:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $currentVariant['show_opening_hours'] ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Show Social:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $currentVariant['show_social'] ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Show Newsletter:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $currentVariant['show_newsletter'] ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View Path:</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $currentVariant['view'] ?? 'N/A' }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No variant information available.</p>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Actions
            </x-slot>

            <div class="flex gap-4">
                <x-filament::button type="submit">
                    Save Footer Settings
                </x-filament::button>
            </div>
        </x-filament::section>
    </form>
</x-filament-panels::page>

