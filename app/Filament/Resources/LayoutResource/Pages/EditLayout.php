<?php

declare(strict_types=1);

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Domain\Layouts\Models\Layout;
use App\Filament\Resources\LayoutResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Layout $record) {
                    if ($record->contents()->count() > 0) {
                        throw new \Exception('Cannot delete layout that is assigned to content. Remove assignments first.');
                    }
                }),
            Action::make('setAsDefault')
                ->label('Set as Default')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $layout = $this->record;
                    // Unset other defaults for this business
                    Layout::forBusiness($layout->business_id)
                        ->where('id', '!=', $layout->id)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                    // Set this as default
                    $layout->update(['is_default' => true]);
                    $this->refreshFormData(['is_default']);
                    \Filament\Notifications\Notification::make()
                        ->title('Layout set as default')
                        ->success()
                        ->send();
                })
                ->visible(fn () => ! $this->record->is_default),
            Action::make('compile')
                ->label('Compile Layout')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $layout = $this->record;
                    // Trigger layout compilation (if service exists)
                    try {
                        // This would call the compilation service
                        // For now, just update compiled_at
                        $layout->update(['compiled_at' => now()]);
                        \Filament\Notifications\Notification::make()
                            ->title('Layout compilation triggered')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Compilation failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure regions is an array
        if (isset($data['regions']) && ! is_array($data['regions'])) {
            $data['regions'] = [];
        }

        // If setting as default, unset other defaults for this business
        if (isset($data['is_default']) && $data['is_default']) {
            Layout::forBusiness($data['business_id'])
                ->where('id', '!=', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
