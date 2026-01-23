<x-filament-panels::page>
    @php
        // Get backups list
        $backups = $this->getBackups();

        // SVG Icons for Info Section
        $backupIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />';
        
        $restoreIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
        
        $scheduleIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';

        $storageIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />';
    @endphp

    <div class="space-y-6">
        <!-- Backups List -->
        <x-filament::section>
            <x-slot name="heading">
                Existing Backups
            </x-slot>
            <x-slot name="description">
                Manage your database and files backups
            </x-slot>

            @if(count($backups) > 0)
                <div class="backup-restore-table-container" style="width: 100%;">
                    <table class="backup-restore-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th>
                                    <div class="backup-restore-actions-header">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>
                                        <div class="backup-restore-filename">
                                            {{ $backup['filename'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="backup-restore-type-badge {{ $backup['type'] === 'database' ? 'database' : 'files' }}">
                                            {{ $backup['type'] === 'database' ? 'Database' : 'Files' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="backup-restore-size">
                                            {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                        </span>
                                    </td>
                                    <td>
                                        <span class="backup-restore-created">
                                            {{ date('Y-m-d H:i:s', $backup['created_at']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="backup-restore-actions">
                                            <x-filament::button
                                                size="sm"
                                                color="gray"
                                                wire:click="downloadBackup('{{ $backup['filename'] }}')"
                                                icon="heroicon-o-arrow-down-tray"
                                            >
                                                Download
                                            </x-filament::button>
                                            <x-filament::button
                                                size="sm"
                                                color="danger"
                                                wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                                icon="heroicon-o-trash"
                                                wire:confirm="Are you sure you want to delete this backup?"
                                            >
                                                Delete
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="backup-restore-empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3>No backups</h3>
                    <p>Get started by creating a new backup.</p>
                </div>
            @endif
        </x-filament::section>

        <!-- Info Section -->
        <x-filament::section>
            <x-slot name="heading">
                About Backups
            </x-slot>
            <x-slot name="description">
                Best practices for backup and restore operations
            </x-slot>

            <div class="my-info-list">
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            {!! $backupIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Regular Backups:</strong>
                        Create backups regularly before major updates or changes. Database backups include all your data, while file backups include uploaded media and configuration files.
                    </div>
                </div>
                
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            {!! $storageIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Storage Location:</strong>
                        Backups are stored in <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded text-xs">storage/app/backups</code>. Make sure to download and store backups in a safe location outside your server.
                    </div>
                </div>
                
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            {!! $scheduleIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Automated Backups:</strong>
                        For production environments, consider setting up automated backups using Laravel's task scheduler or external backup services.
                    </div>
                </div>
                
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            {!! $restoreIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Restore Process:</strong>
                        To restore a database backup, use MySQL command line or phpMyAdmin. For file backups, extract the ZIP archive and restore files to their original locations.
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
