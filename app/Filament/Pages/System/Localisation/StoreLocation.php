<?php

namespace App\Filament\Pages\System\Localisation;

use App\Domain\Settings\Services\GetSettingsService;
use App\Domain\Settings\Services\UpdateSettingsService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StoreLocation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.store-location';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Store Location';

    public ?array $data = [];

    public function mount(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $getSettings = app(GetSettingsService::class);
        $storeLocationSettings = $getSettings->getGroup('store_location');

        $this->form->fill([
            'store_name' => $storeLocationSettings['store_name'] ?? config('app.name'),
            'address_street' => $storeLocationSettings['address_street'] ?? '',
            'address_city' => $storeLocationSettings['address_city'] ?? '',
            'address_region' => $storeLocationSettings['address_region'] ?? '',
            'address_postal_code' => $storeLocationSettings['address_postal_code'] ?? '',
            'address_country' => $storeLocationSettings['address_country'] ?? 'GR',
            'phone' => $storeLocationSettings['phone'] ?? '',
            'email' => $storeLocationSettings['email'] ?? config('mail.from.address', ''),
            'latitude' => $storeLocationSettings['latitude'] ?? '',
            'longitude' => $storeLocationSettings['longitude'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Store Information')
                    ->description('Basic information about your store')
                    ->components([
                        TextInput::make('store_name')
                            ->label('Store Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The name of your store'),
                    ]),
                Section::make('Address')
                    ->description('Physical address of your store')
                    ->components([
                        TextInput::make('address_street')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->helperText('Street name and number'),
                        TextInput::make('address_city')
                            ->label('City')
                            ->maxLength(100)
                            ->helperText('City name'),
                        TextInput::make('address_region')
                            ->label('Region/State')
                            ->maxLength(100)
                            ->helperText('State, province, or region'),
                        TextInput::make('address_postal_code')
                            ->label('Postal Code')
                            ->maxLength(20)
                            ->helperText('ZIP or postal code'),
                        TextInput::make('address_country')
                            ->label('Country')
                            ->maxLength(2)
                            ->default('GR')
                            ->helperText('ISO 3166-1 alpha-2 country code (e.g., GR, US, UK)'),
                    ]),
                Section::make('Contact Information')
                    ->description('Contact details for your store')
                    ->components([
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50)
                            ->helperText('Store phone number'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Store email address'),
                    ]),
                Section::make('Coordinates')
                    ->description('Geographic coordinates for map integration')
                    ->components([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->helperText('Latitude coordinate (e.g., 37.9838)'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->helperText('Longitude coordinate (e.g., 23.7275)'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $updateService = app(UpdateSettingsService::class);

        $group = 'store_location';
        $numericFields = ['latitude', 'longitude'];

        foreach ($data as $key => $value) {
            $type = 'string';
            if (in_array($key, $numericFields)) {
                $type = 'decimal';
            }

            $updateService->execute($key, $value, $type, $group);
        }

        Notification::make()
            ->title('Store Location Saved')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return 'Store Location';
    }
}
