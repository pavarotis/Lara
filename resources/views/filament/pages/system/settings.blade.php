<x-filament-panels::page>
    @if($newStoreId)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prevent navigation when there's an unsaved new store
                window.addEventListener('beforeunload', function(e) {
                    @if($newStoreId)
                        e.preventDefault();
                        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        return e.returnValue;
                    @endif
                });

                // Prevent Livewire navigation
                Livewire.hook('morph.updated', ({ component }) => {
                    // This will be handled by the updatedActiveTab method
                });
            });
        </script>
    @endif
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'general'"
            wire:click="switchToTab('general')"
        >
            General
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'email'"
            wire:click="switchToTab('email')"
        >
            Email
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'security'"
            wire:click="switchToTab('security')"
        >
            Security
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'localization'"
            wire:click="switchToTab('localization')"
        >
            Localization
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'store_location'"
            wire:click="switchToTab('store_location')"
        >
            Stores Location
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div class="mt-6">
        @if($activeTab === 'general')
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        @elseif($activeTab === 'email')
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Email Settings
                    </x-filament::button>
                </div>
            </form>
        @elseif($activeTab === 'security')
            <x-filament::tabs>
                <x-filament::tabs.item
                    :active="$activeSecurityTab === 'site'"
                    wire:click="$set('activeSecurityTab', 'site')"
                >
                    Site
                </x-filament::tabs.item>
                <x-filament::tabs.item
                    :active="$activeSecurityTab === 'admin'"
                    wire:click="$set('activeSecurityTab', 'admin')"
                >
                    Admin
                </x-filament::tabs.item>
            </x-filament::tabs>

            <div class="mt-6">
                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="mt-6">
                        <x-filament::button type="submit">
                            Save Security Settings
                        </x-filament::button>
                    </div>
                </form>
            </div>
        @elseif($activeTab === 'localization')
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Localization Settings
                    </x-filament::button>
                </div>
            </form>
        @elseif($activeTab === 'store_location')
            <x-filament::tabs>
                <x-filament::tabs.item
                    :active="$activeStoreTab === 'main'"
                    wire:click="switchToMainStore"
                >
                    Main Store
                </x-filament::tabs.item>
                @if($newStoreId)
                    <x-filament::tabs.item
                        :active="$activeStoreTab === $newStoreId"
                        wire:click="$set('activeStoreTab', $newStoreId)"
                    >
                        {{ $newStoreName }}
                    </x-filament::tabs.item>
                @endif
                @foreach($this->getStores() as $storeId => $store)
                    <x-filament::tabs.item
                        :active="$activeStoreTab === $storeId"
                        wire:click="switchToStore('{{ $storeId }}')"
                    >
                        {{ $store['store_name'] ?? 'Store' }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>

            <div class="mt-6">
                <div class="flex justify-end gap-2 mb-8" style="padding: 1rem 0;">
                    <x-filament::button
                        wire:click="createNewStoreTab"
                        icon="heroicon-o-plus"
                    >
                        Add New Store
                    </x-filament::button>
                    @if($activeStoreTab !== 'main')
                        <x-filament::button
                            wire:click="deleteCurrentStore"
                            color="danger"
                            icon="heroicon-o-trash"
                            wire:confirm="Are you sure you want to delete this store? This action cannot be undone."
                        >
                            Delete Store
                        </x-filament::button>
                    @endif
                </div>

                @if($activeStoreTab === 'main')
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg mb-6" style="padding: 1.5rem;">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Note:</strong> The Main Store information is read-only and is automatically synchronized from the General Settings tab.<br>
                            <a href="#" wire:click.prevent="$set('activeTab', 'general')" class="font-medium transition-colors duration-200" style="color: #d4a574;" onmouseover="this.style.color='#f59e0b';" onmouseout="this.style.color='#d4a574';">
                                To modify these fields, please go to the General Settings tab.
                            </a>
                        </p>
                    </div>
                    <div>
                        {{ $this->form }}
                    </div>
                @elseif($newStoreId && $activeStoreTab === $newStoreId)
                    <form wire:submit="save">
                        {{ $this->form }}

                        <div class="mt-6">
                            <x-filament::button type="submit">
                                Add Store
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                color="gray"
                                wire:click="$set('newStoreId', null); $set('newStoreName', 'NEW STORE'); $set('activeStoreTab', 'main')"
                            >
                                Cancel
                            </x-filament::button>
                        </div>
                    </form>
                @elseif($selectedStoreId && $activeStoreTab === $selectedStoreId)
                    <form wire:submit="save">
                        {{ $this->form }}

                        <div class="mt-6">
                            <x-filament::button type="submit">
                                Update Store
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                color="gray"
                                wire:click="$set('selectedStoreId', null); $set('activeStoreTab', 'main')"
                            >
                                Cancel
                            </x-filament::button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
