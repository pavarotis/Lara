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

class Footer extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.cms.footer';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Footer';

    public ?string $footerVariant = null;

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
        $this->footerVariant = $themeToken?->footer_variant ?? 'simple';

        $this->form->fill([
            'footer_variant' => $this->footerVariant,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $footerVariants = collect(config('footer_variants', []))->mapWithKeys(function ($config, $key) {
            return [$key => $config['name'] ?? $key];
        })->toArray();

        return $schema
            ->components([
                Section::make('Footer Variant Selection')
                    ->description('Select the footer variant for the active business')
                    ->components([
                        Select::make('footer_variant')
                            ->label('Footer Variant')
                            ->options($footerVariants)
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
            'footer_variant' => $data['footer_variant'],
        ]);

        Notification::make()
            ->title('Footer variant updated')
            ->success()
            ->send();

        // Refresh variant info
        $themeToken = \App\Domain\Themes\Models\ThemeToken::where('business_id', $this->business->id)->first();
        $this->footerVariant = $themeToken?->footer_variant ?? 'simple';
    }

    public function getTitle(): string
    {
        return 'Footer Management';
    }
}
