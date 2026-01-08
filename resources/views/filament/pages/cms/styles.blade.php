<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::section>
            <x-slot name="heading">
                Actions
            </x-slot>

            <div class="flex gap-4">
                <x-filament::button type="submit">
                    Save Theme Settings
                </x-filament::button>
            </div>
        </x-filament::section>
    </form>
</x-filament-panels::page>

