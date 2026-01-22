<x-filament-panels::page>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div class="space-y-6">
        <!-- Upload Section -->
        <x-filament::section>
            <x-slot name="heading">
                Upload Vqmod File
            </x-slot>
            <x-slot name="description">
                Upload a new XML vqmod file
            </x-slot>

            <form wire:submit="uploadFile">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        Upload File
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <!-- Files List Section -->
        <x-filament::section>
            <x-slot name="heading">
                Vqmod Files
            </x-slot>
            <x-slot name="description">
                Manage your vqmod XML files
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                File Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Size
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Modified
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->getFiles() as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $file['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($file['enabled'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Enabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Disabled
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format($file['size'] / 1024, 2) }} KB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ date('Y-m-d H:i:s', $file['modified']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <x-filament::button
                                            wire:click="editFile('{{ $file['name'] }}')"
                                            size="sm"
                                            color="gray"
                                            icon="heroicon-o-pencil"
                                        >
                                            Edit
                                        </x-filament::button>
                                        
                                        @if($file['enabled'])
                                            <x-filament::button
                                                wire:click="disableFile('{{ $file['name'] }}')"
                                                wire:confirm="Are you sure you want to disable this file?"
                                                size="sm"
                                                color="warning"
                                                icon="heroicon-o-x-circle"
                                            >
                                                Disable
                                            </x-filament::button>
                                        @else
                                            <x-filament::button
                                                wire:click="enableFile('{{ $file['name'] }}')"
                                                size="sm"
                                                color="success"
                                                icon="heroicon-o-check-circle"
                                            >
                                                Enable
                                            </x-filament::button>
                                        @endif
                                        
                                        <x-filament::button
                                            wire:click="deleteFile('{{ $file['name'] }}')"
                                            wire:confirm="Are you sure you want to delete this file? This action cannot be undone."
                                            size="sm"
                                            color="danger"
                                            icon="heroicon-o-trash"
                                        >
                                            Delete
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No vqmod files found. Upload a file to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    <!-- Edit File Modal -->
    <div 
        x-data="{ show: false }"
        x-show="show"
        x-cloak
        @open-edit-modal.window="show = true"
        @close-edit-modal.window="show = false"
        @keydown.escape.window="show = false; $wire.cancelEdit()"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        wire:ignore.self
    >
        <div 
            class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" 
            @click="show = false; $wire.cancelEdit()"
            x-show="show"
            x-transition
        ></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Edit: {{ $editingFile ?? 'File' }}
                        </h3>
                        <button 
                            type="button"
                            @click="show = false; $wire.cancelEdit()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form wire:submit="saveFile" class="flex-1 flex flex-col overflow-hidden">
                    <div class="flex-1 p-6 overflow-y-auto">
                        <textarea
                            wire:model="editingContent"
                            rows="25"
                            class="w-full font-mono text-sm border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-900 dark:text-white"
                        ></textarea>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                        <x-filament::button
                            type="button"
                            @click="show = false; $wire.cancelEdit()"
                            color="gray"
                        >
                            Cancel
                        </x-filament::button>
                        <x-filament::button
                            type="submit"
                            color="primary"
                        >
                            Save
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
