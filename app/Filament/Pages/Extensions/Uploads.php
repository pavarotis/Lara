<?php

namespace App\Filament\Pages\Extensions;

use App\Domain\Businesses\Models\Business;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Models\MediaFolder;
use App\Domain\Media\Services\UploadMediaService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class Uploads extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.extensions.uploads';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Uploads';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $business = Business::active()->first();
        $folders = $business
            ? MediaFolder::where('business_id', $business->id)
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn ($folder) => [$folder->id => $folder->name])
                ->prepend('Root (No Folder)', null)
            : [];

        return $schema
            ->components([
                Section::make('Quick Upload')
                    ->description('Upload files directly to the media library. Files will be organized and available for use in content, products, and other areas.')
                    ->components([
                        Grid::make(2)
                            ->components([
                                FileUpload::make('files')
                                    ->label('Files')
                                    ->multiple()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'application/pdf'])
                                    ->maxSize(10240) // 10MB
                                    ->directory('temp')
                                    ->visibility('private')
                                    ->required()
                                    ->imagePreviewHeight('120')
                                    ->helperText('Supported: Images (JPEG, PNG, GIF, WebP), Videos (MP4), PDFs. Max 10MB per file.')
                                    ->columnSpan(2),
                                Select::make('folder_id')
                                    ->label('Folder')
                                    ->options($folders)
                                    ->nullable()
                                    ->helperText('Optional: Select a folder to organize your files')
                                    ->columnSpan(1),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function upload(): void
    {
        $data = $this->form->getState();
        $business = Business::active()->first();

        if (! $business) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('No active business found.')
                ->send();

            return;
        }

        if (empty($data['files'])) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('No files selected.')
                ->send();

            return;
        }

        $uploadService = app(UploadMediaService::class);
        $uploadedCount = 0;
        $errors = [];

        foreach ($data['files'] as $storedPath) {
            try {
                $fullPath = Storage::disk('local')->path($storedPath);

                if (! file_exists($fullPath)) {
                    $errors[] = basename($storedPath).' (file not found)';

                    continue;
                }

                // Create UploadedFile instance from stored file
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($fullPath),
                    mime_content_type($fullPath),
                    null,
                    true
                );

                $uploadService->execute($business, $file, $data['folder_id'] ?? null);
                $uploadedCount++;

                // Delete temp file
                Storage::disk('local')->delete($storedPath);
            } catch (\Exception $e) {
                $errors[] = basename($storedPath).': '.$e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            Notification::make()
                ->title('Upload Successful')
                ->success()
                ->body("{$uploadedCount} file(s) uploaded successfully!")
                ->send();
        }

        if (! empty($errors)) {
            Notification::make()
                ->title('Some Uploads Failed')
                ->warning()
                ->body(implode(', ', $errors))
                ->send();
        }

        if ($uploadedCount > 0 || ! empty($errors)) {
            $this->form->fill();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openMediaLibrary')
                ->label('Open Media Library')
                ->icon('heroicon-o-photo')
                ->url(route('admin.media.index'))
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return 'Uploads';
    }

    public function getRecentMedia()
    {
        $business = Business::active()->first();
        if (! $business) {
            return collect();
        }

        return Media::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
