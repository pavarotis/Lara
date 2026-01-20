<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Services\UpdateThemeTokensService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Header extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.cms.header';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Header';

    public ?string $headerVariant = null;

    public ?Business $business = null;

    public function mount(): void
    {
        $this->business = Business::active()->first();
        if (! $this->business) {
            Notification::make()
                ->title('No active business found')
                ->danger()
                ->send();

            return;
        }

        $themeToken = \App\Domain\Themes\Models\ThemeToken::where('business_id', $this->business->id)->first();
        $this->headerVariant = $themeToken?->header_variant ?? 'minimal';

        $this->form->fill([
            'header_variant' => $this->headerVariant,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $headerVariants = collect(config('header_variants', []))->mapWithKeys(function ($config, $key) {
            return [$key => $config['name'] ?? $key];
        })->toArray();

        return $schema
            ->components([
                Section::make('Header Variant Selection')
                    ->description('Select the header variant for the active business')
                    ->components([
                        Select::make('header_variant')
                            ->label('Header Variant')
                            ->options($headerVariants)
                            ->required()
                            ->live()
                            ->helperText('Changes will apply immediately to the public site'),
                    ]),
            ]);
    }

    public function save(): void
    {
        if (! $this->business) {
            return;
        }

        $data = $this->form->getState();

        app(UpdateThemeTokensService::class)->execute($this->business, [
            'header_variant' => $data['header_variant'],
        ]);

        Notification::make()
            ->title('Header variant updated')
            ->success()
            ->send();

        // Refresh variant info
        $themeToken = \App\Domain\Themes\Models\ThemeToken::where('business_id', $this->business->id)->first();
        $this->headerVariant = $themeToken?->header_variant ?? 'minimal';
    }

    public function getTitle(): string
    {
        return 'Header Management';
    }
}
