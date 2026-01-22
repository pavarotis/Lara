<?php

namespace App\Filament\Pages\System\Maintenance;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class ErrorLogs extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 19;

    protected string $view = 'filament.pages.system.maintenance.error-logs';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Error Logs';

    public ?string $selectedLogFile = null;

    public ?string $logContent = null;

    public function getTitle(): string
    {
        return 'Error Logs';
    }

    public function mount(): void
    {
        $this->loadLogFiles();

        // Auto-load laravel.log if it exists
        $laravelLogPath = storage_path('logs/laravel.log');
        if (File::exists($laravelLogPath)) {
            $this->loadLogFile('laravel.log');
        }
    }

    public function getLogFiles(): array
    {
        $logPath = storage_path('logs');
        $files = [];

        if (File::exists($logPath)) {
            $allFiles = File::files($logPath);
            foreach ($allFiles as $file) {
                if ($file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime(),
                    ];
                }
            }
        }

        // Sort by modified time (newest first)
        usort($files, fn ($a, $b) => $b['modified'] <=> $a['modified']);

        return $files;
    }

    public function loadLogFile(string $filename): void
    {
        $this->selectedLogFile = $filename;
        $logPath = storage_path('logs/'.$filename);

        if (File::exists($logPath)) {
            // Read last 1000 lines to avoid memory issues
            $lines = file($logPath);
            $totalLines = count($lines);
            $startLine = max(0, $totalLines - 1000);
            $this->logContent = implode('', array_slice($lines, $startLine));
        } else {
            $this->logContent = null;
            Notification::make()
                ->title('Log file not found')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->loadLogFiles();
                    Notification::make()
                        ->title('Logs refreshed')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function loadLogFiles(): void
    {
        // This method is called on mount, can be used for initialization
    }
}
