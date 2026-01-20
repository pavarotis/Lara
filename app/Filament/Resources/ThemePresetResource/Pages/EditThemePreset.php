<?php

declare(strict_types=1);

namespace App\Filament\Resources\ThemePresetResource\Pages;

use App\Domain\Themes\Models\ThemePreset;
use App\Filament\Resources\ThemePresetResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditThemePreset extends EditRecord
{
    protected static string $resource = ThemePresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (ThemePreset $record) {
                    if ($record->businesses()->count() > 0) {
                        throw new \Exception('Cannot delete skin that is assigned to businesses. Remove assignments first.');
                    }
                }),
            Action::make('toggleActive')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_active' => ! $this->record->is_active]);
                    $this->refreshFormData(['is_active']);
                    \Filament\Notifications\Notification::make()
                        ->title('Skin '.($this->record->is_active ? 'activated' : 'deactivated'))
                        ->success()
                        ->send();
                }),
            Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $original = $this->record;
                    $newSlug = $original->slug.'-copy-'.Str::random(4);
                    $newName = $original->name.' (Copy)';

                    $duplicate = ThemePreset::create([
                        'slug' => $newSlug,
                        'name' => $newName,
                        'tokens' => $original->tokens,
                        'default_modules' => $original->default_modules,
                        'default_header_variant' => $original->default_header_variant,
                        'default_footer_variant' => $original->default_footer_variant,
                        'is_active' => false, // Duplicates start as inactive
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Skin duplicated')
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.theme-presets.edit', $duplicate);
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert arrays to JSON strings for CodeEditor
        if (isset($data['tokens']) && is_array($data['tokens'])) {
            $data['tokens'] = json_encode($data['tokens'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        if (isset($data['default_modules']) && is_array($data['default_modules'])) {
            $data['default_modules'] = json_encode($data['default_modules'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure tokens is valid JSON
        if (isset($data['tokens']) && is_string($data['tokens'])) {
            $data['tokens'] = json_decode($data['tokens'], true) ?? [];
        }

        // Ensure default_modules is valid JSON
        if (isset($data['default_modules']) && is_string($data['default_modules'])) {
            $data['default_modules'] = json_decode($data['default_modules'], true) ?? [];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
