<?php

namespace App\Filament\Pages;

use App\Domain\Vqmod\Services\VqmodManagerService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class VqmodManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.vqmod-manager';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Vqmod Manager';

    public ?array $data = [];

    public ?string $editingFile = null;

    public ?string $editingContent = null;

    protected VqmodManagerService $vqmodService;

    public function boot(): void
    {
        $this->vqmodService = app(VqmodManagerService::class);
    }

    public function getTitle(): string
    {
        return 'Vqmod Manager';
    }

    public function getFiles(): array
    {
        return $this->vqmodService->getAllFiles();
    }

    public function enableFile(string $filename): void
    {
        if ($this->vqmodService->enableFile($filename)) {
            Notification::make()
                ->title('File Enabled')
                ->success()
                ->body("The file '{$filename}' has been enabled.")
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("Failed to enable file '{$filename}'.")
                ->send();
        }
    }

    public function disableFile(string $filename): void
    {
        if ($this->vqmodService->disableFile($filename)) {
            Notification::make()
                ->title('File Disabled')
                ->success()
                ->body("The file '{$filename}' has been disabled.")
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("Failed to disable file '{$filename}'.")
                ->send();
        }
    }

    public function deleteFile(string $filename): void
    {
        if ($this->vqmodService->deleteFile($filename)) {
            Notification::make()
                ->title('File Deleted')
                ->success()
                ->body("The file '{$filename}' has been deleted.")
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("Failed to delete file '{$filename}'.")
                ->send();
        }
    }

    public function editFile(string $filename): void
    {
        $content = $this->vqmodService->getFileContent($filename);

        if ($content === null) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("File '{$filename}' not found.")
                ->send();

            return;
        }

        $this->editingFile = $filename;
        $this->editingContent = $content;
        $this->dispatch('open-edit-modal');
    }

    public function saveFile(): void
    {
        if (! $this->editingFile || ! $this->editingContent) {
            return;
        }

        if ($this->vqmodService->saveFileContent($this->editingFile, $this->editingContent)) {
            Notification::make()
                ->title('File Saved')
                ->success()
                ->body("The file '{$this->editingFile}' has been saved.")
                ->send();

            $this->editingFile = null;
            $this->editingContent = null;
            $this->dispatch('close-edit-modal');
        } else {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("Failed to save file '{$this->editingFile}'.")
                ->send();
        }
    }

    public function cancelEdit(): void
    {
        $this->editingFile = null;
        $this->editingContent = null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Upload Vqmod File')
                    ->schema([
                        FileUpload::make('file')
                            ->label('XML File')
                            ->acceptedFileTypes(['application/xml', 'text/xml'])
                            ->directory('vqmod/temp')
                            ->visibility('private')
                            ->required()
                            ->maxSize(1024),
                        Toggle::make('enabled')
                            ->label('Enable after upload')
                            ->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function uploadFile(): void
    {
        $data = $this->form->getState();

        if (! isset($data['file'])) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('No file selected.')
                ->send();

            return;
        }

        $storedPath = $data['file'];
        $tempPath = Storage::path($storedPath);
        $filename = basename($storedPath);
        $enabled = $data['enabled'] ?? true;

        // Move file to correct location
        $enabledPath = storage_path('app/vqmod/xml');
        $disabledPath = storage_path('app/vqmod/xml/disabled');

        $destination = $enabled ? $enabledPath : $disabledPath;

        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $destinationPath = $destination.'/'.$filename;

        // Check if file already exists
        if (file_exists($destinationPath)) {
            Storage::delete($storedPath);
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("File '{$filename}' already exists.")
                ->send();

            return;
        }

        if (rename($tempPath, $destinationPath)) {
            Notification::make()
                ->title('File Uploaded')
                ->success()
                ->body("The file '{$filename}' has been uploaded.")
                ->send();

            $this->form->fill();
        } else {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body("Failed to upload file '{$filename}'.")
                ->send();
        }
    }
}
