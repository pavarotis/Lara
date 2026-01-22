<?php

namespace App\Filament\Pages\System;

use App\Domain\Settings\Services\GetSettingsService;
use App\Domain\Settings\Services\UpdateSettingsService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.system.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    public ?array $data = [];

    public string $activeTab = 'general';

    public string $activeSecurityTab = 'site';

    public string $activeStoreTab = 'main';

    public ?string $selectedStoreId = null;

    public ?string $newStoreId = null;

    public string $newStoreName = 'NEW STORE';

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/settings route.
     * This will register the Filament page at /admin/system-settings with route name filament.admin.pages.system-settings.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'system-settings';
    }

    public function mount(): void
    {
        $this->loadTabData();
    }

    public function updatedActiveTab(): void
    {
        // Prevent tab change if there's an unsaved new store
        if ($this->activeTab === 'store_location' && $this->newStoreId && $this->activeStoreTab !== $this->newStoreId) {
            // User is trying to leave the new store tab within store_location
            // This is handled by switchToMainStore and switchToStore methods
        }
        $this->loadTabData();
    }

    /**
     * Switch to any tab (with check for unsaved new store)
     */
    public function switchToTab(string $tab): void
    {
        // Prevent tab change if there's an unsaved new store
        if ($this->newStoreId && $this->activeTab === 'store_location') {
            Notification::make()
                ->title('Unsaved Changes')
                ->body('Please save or delete the new store before changing tabs.')
                ->warning()
                ->send();

            return;
        }
        $this->activeTab = $tab;
        $this->loadTabData();
    }

    public function updatedActiveSecurityTab(): void
    {
        $this->loadTabData();
    }

    private function loadTabData(): void
    {
        $getSettings = app(GetSettingsService::class);

        if ($this->activeTab === 'general') {
            $generalSettings = $getSettings->getGroup('general');
            $storeLocationSettings = $getSettings->getGroup('store_location');
            $this->form->fill([
                'site_name' => $generalSettings['site_name'] ?? '',
                'site_email' => $generalSettings['site_email'] ?? '',
                'site_phone' => $generalSettings['site_phone'] ?? ($storeLocationSettings['phone'] ?? ''),
                'address_street' => $generalSettings['address_street'] ?? ($storeLocationSettings['address_street'] ?? ''),
                'address_city' => $generalSettings['address_city'] ?? ($storeLocationSettings['address_city'] ?? ''),
                'address_region' => $generalSettings['address_region'] ?? ($storeLocationSettings['address_region'] ?? ''),
                'address_postal_code' => $generalSettings['address_postal_code'] ?? ($storeLocationSettings['address_postal_code'] ?? ''),
                'address_country' => $generalSettings['address_country'] ?? ($storeLocationSettings['address_country'] ?? 'GR'),
                'latitude' => $generalSettings['latitude'] ?? ($storeLocationSettings['latitude'] ?? ''),
                'longitude' => $generalSettings['longitude'] ?? ($storeLocationSettings['longitude'] ?? ''),
                'enable_login_signup' => filter_var($generalSettings['enable_login_signup'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'maintenance_mode' => filter_var($generalSettings['maintenance_mode'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);
        } elseif ($this->activeTab === 'email') {
            $emailSettings = $getSettings->getGroup('email');
            $this->form->fill([
                'mail_mailer' => $emailSettings['mail_mailer'] ?? '',
                'mail_host' => $emailSettings['mail_host'] ?? '',
                'mail_port' => $emailSettings['mail_port'] ?? '',
                'mail_username' => $emailSettings['mail_username'] ?? '',
                'mail_password' => $emailSettings['mail_password'] ?? '',
                'mail_encryption' => $emailSettings['mail_encryption'] ?? '',
                'mail_from_address' => $emailSettings['mail_from_address'] ?? '',
                'mail_from_name' => $emailSettings['mail_from_name'] ?? '',
            ]);
        } elseif ($this->activeTab === 'security') {
            $securitySettings = $getSettings->getGroup('security');
            if ($this->activeSecurityTab === 'site') {
                $this->form->fill([
                    // Frontend/Regular Users Password Requirements
                    'password_min_length' => $securitySettings['password_min_length'] ?? 8,
                    'password_require_uppercase' => filter_var($securitySettings['password_require_uppercase'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'password_require_lowercase' => filter_var($securitySettings['password_require_lowercase'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'password_require_numbers' => filter_var($securitySettings['password_require_numbers'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'password_require_symbols' => filter_var($securitySettings['password_require_symbols'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    // Site Session & Security
                    'site_session_lifetime' => $securitySettings['site_session_lifetime'] ?? 120,
                    'site_two_factor_enabled' => filter_var($securitySettings['site_two_factor_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            } elseif ($this->activeSecurityTab === 'admin') {
                $this->form->fill([
                    // Admin Users Password Requirements
                    'admin_password_min_length' => $securitySettings['admin_password_min_length'] ?? 12,
                    'admin_password_require_uppercase' => filter_var($securitySettings['admin_password_require_uppercase'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'admin_password_require_lowercase' => filter_var($securitySettings['admin_password_require_lowercase'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'admin_password_require_numbers' => filter_var($securitySettings['admin_password_require_numbers'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'admin_password_require_symbols' => filter_var($securitySettings['admin_password_require_symbols'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    // Admin Session & Security
                    'admin_session_lifetime' => $securitySettings['admin_session_lifetime'] ?? 120,
                    'admin_two_factor_enabled' => filter_var($securitySettings['admin_two_factor_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        } elseif ($this->activeTab === 'localization') {
            $localizationSettings = $getSettings->getGroup('localization');
            $this->form->fill([
                'default_language' => $localizationSettings['default_language'] ?? config('app.locale', 'en'),
                'default_currency' => $localizationSettings['default_currency'] ?? 'EUR',
                'default_timezone' => $localizationSettings['default_timezone'] ?? config('app.timezone', 'Europe/Athens'),
                'date_format' => $localizationSettings['date_format'] ?? 'd/m/Y',
                'time_format' => $localizationSettings['time_format'] ?? 'H:i:s',
            ]);
        } elseif ($this->activeTab === 'store_location') {
            if ($this->activeStoreTab === 'main') {
                // Load from General settings for Main store
                $generalSettings = $getSettings->getGroup('general');
                $storeLocationSettings = $getSettings->getGroup('store_location');
                $this->form->fill([
                    'store_name' => $storeLocationSettings['store_name'] ?? ($generalSettings['site_name'] ?? ''),
                    'address_street' => $storeLocationSettings['address_street'] ?? ($generalSettings['address_street'] ?? ''),
                    'address_city' => $storeLocationSettings['address_city'] ?? ($generalSettings['address_city'] ?? ''),
                    'address_region' => $storeLocationSettings['address_region'] ?? ($generalSettings['address_region'] ?? ''),
                    'address_postal_code' => $storeLocationSettings['address_postal_code'] ?? ($generalSettings['address_postal_code'] ?? ''),
                    'address_country' => $storeLocationSettings['address_country'] ?? ($generalSettings['address_country'] ?? 'GR'),
                    'phone' => $storeLocationSettings['phone'] ?? ($generalSettings['site_phone'] ?? ''),
                    'email' => $storeLocationSettings['email'] ?? ($generalSettings['site_email'] ?? ''),
                    'latitude' => $storeLocationSettings['latitude'] ?? ($generalSettings['latitude'] ?? ''),
                    'longitude' => $storeLocationSettings['longitude'] ?? ($generalSettings['longitude'] ?? ''),
                ]);
            } elseif ($this->activeStoreTab !== 'main') {
                // This is either a new store tab or editing existing store
                if ($this->selectedStoreId && $this->activeStoreTab === $this->selectedStoreId) {
                    // Load selected store for editing
                    $stores = $this->getStores();
                    $store = $stores[$this->selectedStoreId] ?? null;
                    if ($store) {
                        $this->form->fill([
                            'store_name' => $store['store_name'] ?? '',
                            'address_street' => $store['address_street'] ?? '',
                            'address_city' => $store['address_city'] ?? '',
                            'address_region' => $store['address_region'] ?? '',
                            'address_postal_code' => $store['address_postal_code'] ?? '',
                            'address_country' => $store['address_country'] ?? 'GR',
                            'phone' => $store['phone'] ?? '',
                            'email' => $store['email'] ?? '',
                            'latitude' => $store['latitude'] ?? '',
                            'longitude' => $store['longitude'] ?? '',
                        ]);
                    }
                } elseif ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
                    // Empty fields for new store
                    $this->form->fill([
                        'store_name' => $this->newStoreName,
                        'address_street' => '',
                        'address_city' => '',
                        'address_region' => '',
                        'address_postal_code' => '',
                        'address_country' => 'GR',
                        'phone' => '',
                        'email' => '',
                        'latitude' => '',
                        'longitude' => '',
                    ]);
                }
            }
        }
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        if ($this->activeTab === 'general') {
            $components[] = Section::make('General Settings')
                ->components([
                    TextInput::make('site_name')
                        ->label('Site Name')
                        ->maxLength(255)
                        ->placeholder('Enter site name')
                        ->helperText('The name of your website'),
                ]);
            $components[] = Section::make('Contact Information')
                ->description('Contact details for the site')
                ->components([
                    TextInput::make('site_phone')
                        ->label('Phone')
                        ->tel()
                        ->maxLength(50)
                        ->placeholder('Enter site phone number')
                        ->helperText('Default phone number for the site'),
                    TextInput::make('site_email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->placeholder('Enter site email address')
                        ->helperText('Default email address for system notifications'),
                ]);
            $components[] = Section::make('Address')
                ->description('Default address for the site')
                ->components([
                    TextInput::make('address_street')
                        ->label('Street Address')
                        ->maxLength(255)
                        ->placeholder('Enter street address')
                        ->helperText('Street name and number'),
                    TextInput::make('address_city')
                        ->label('City')
                        ->maxLength(100)
                        ->placeholder('Enter city')
                        ->helperText('City name'),
                    TextInput::make('address_region')
                        ->label('Region/State')
                        ->maxLength(100)
                        ->placeholder('Enter region/state')
                        ->helperText('State, province, or region'),
                    TextInput::make('address_postal_code')
                        ->label('Postal Code')
                        ->maxLength(20)
                        ->placeholder('Enter postal code')
                        ->helperText('ZIP or postal code'),
                    TextInput::make('address_country')
                        ->label('Country')
                        ->maxLength(2)
                        ->default('GR')
                        ->placeholder('Enter country code')
                        ->helperText('ISO 3166-1 alpha-2 country code (e.g., GR, US, UK)'),
                ]);
            $components[] = Section::make('Coordinates')
                ->description('Geographic coordinates for map integration')
                ->components([
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric()
                        ->step(0.000001)
                        ->maxLength(20)
                        ->placeholder('Enter latitude')
                        ->helperText('Latitude coordinate (e.g., 37.9838)'),
                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric()
                        ->step(0.000001)
                        ->maxLength(20)
                        ->placeholder('Enter longitude')
                        ->helperText('Longitude coordinate (e.g., 23.7275)'),
                ]);
            $components[] = Section::make('Site Options')
                ->components([
                    Toggle::make('enable_login_signup')
                        ->label('Enable Login/Sign Up')
                        ->default(true)
                        ->helperText('Enable login and sign up functionality on the site'),
                    Toggle::make('maintenance_mode')
                        ->label('Maintenance Mode')
                        ->helperText('Enable to put the site in maintenance mode'),
                ]);
        } elseif ($this->activeTab === 'email') {
            $components[] = Section::make('From Address')
                ->components([
                    TextInput::make('mail_from_address')
                        ->label('From Email Address')
                        ->email()
                        ->maxLength(255)
                        ->placeholder('Enter from email address')
                        ->helperText('Default "from" email address'),
                    TextInput::make('mail_from_name')
                        ->label('From Name')
                        ->maxLength(255)
                        ->placeholder('Enter from name')
                        ->helperText('Default "from" name'),
                ]);
            $components[] = Section::make('SMTP Configuration')
                ->components([
                    Select::make('mail_mailer')
                        ->label('Mail Driver')
                        ->options([
                            'smtp' => 'SMTP',
                            'sendmail' => 'Sendmail',
                            'mailgun' => 'Mailgun',
                            'ses' => 'Amazon SES',
                            'postmark' => 'Postmark',
                            'resend' => 'Resend',
                            'log' => 'Log',
                            'array' => 'Array (Testing)',
                        ])
                        ->placeholder('Select mail driver')
                        ->helperText('The mail driver to use for sending emails'),
                    TextInput::make('mail_host')
                        ->label('SMTP Host')
                        ->maxLength(255)
                        ->placeholder('Enter SMTP host')
                        ->helperText('SMTP server hostname'),
                    TextInput::make('mail_port')
                        ->label('SMTP Port')
                        ->numeric()
                        ->placeholder('Enter SMTP port')
                        ->helperText('SMTP server port (usually 587 for TLS, 465 for SSL)'),
                    TextInput::make('mail_username')
                        ->label('SMTP Username')
                        ->maxLength(255)
                        ->placeholder('Enter SMTP username')
                        ->helperText('SMTP authentication username'),
                    TextInput::make('mail_password')
                        ->label('SMTP Password')
                        ->password()
                        ->maxLength(255)
                        ->placeholder('Enter SMTP password')
                        ->helperText('SMTP authentication password'),
                    Select::make('mail_encryption')
                        ->label('Encryption')
                        ->options([
                            'tls' => 'TLS',
                            'ssl' => 'SSL',
                            '' => 'None',
                        ])
                        ->placeholder('Select encryption method')
                        ->helperText('SMTP encryption method'),
                ]);
        } elseif ($this->activeTab === 'security') {
            if ($this->activeSecurityTab === 'site') {
                $components[] = Section::make('Frontend/Regular Users Password Requirements')
                    ->description('Password requirements for users registering on the public site')
                    ->components([
                        TextInput::make('password_min_length')
                            ->label('Minimum Length')
                            ->numeric()
                            ->default(8)
                            ->minValue(6)
                            ->maxValue(128)
                            ->required()
                            ->helperText('Minimum password length for regular users (6-128 characters)'),
                        Toggle::make('password_require_uppercase')
                            ->label('Require Uppercase Letters')
                            ->default(true)
                            ->helperText('Regular user passwords must contain at least one uppercase letter'),
                        Toggle::make('password_require_lowercase')
                            ->label('Require Lowercase Letters')
                            ->default(true)
                            ->helperText('Regular user passwords must contain at least one lowercase letter'),
                        Toggle::make('password_require_numbers')
                            ->label('Require Numbers')
                            ->default(true)
                            ->helperText('Regular user passwords must contain at least one number'),
                        Toggle::make('password_require_symbols')
                            ->label('Require Special Characters')
                            ->default(false)
                            ->helperText('Regular user passwords must contain at least one special character'),
                    ]);
                $components[] = Section::make('Site Session Settings')
                    ->components([
                        TextInput::make('site_session_lifetime')
                            ->label('Session Lifetime (minutes)')
                            ->numeric()
                            ->default(120)
                            ->minValue(1)
                            ->maxValue(525600)
                            ->required()
                            ->helperText('How long site user sessions should remain active (1-525600 minutes)'),
                    ]);
                $components[] = Section::make('Site Two-Factor Authentication')
                    ->components([
                        Toggle::make('site_two_factor_enabled')
                            ->label('Enable Two-Factor Authentication')
                            ->default(false)
                            ->helperText('Allow site users to enable 2FA for their accounts'),
                    ]);
            } elseif ($this->activeSecurityTab === 'admin') {
                $components[] = Section::make('Admin Users Password Requirements')
                    ->description('Password requirements for admin users created in the admin panel')
                    ->components([
                        TextInput::make('admin_password_min_length')
                            ->label('Minimum Length')
                            ->numeric()
                            ->default(12)
                            ->minValue(8)
                            ->maxValue(128)
                            ->required()
                            ->helperText('Minimum password length for admin users (8-128 characters)'),
                        Toggle::make('admin_password_require_uppercase')
                            ->label('Require Uppercase Letters')
                            ->default(true)
                            ->helperText('Admin passwords must contain at least one uppercase letter'),
                        Toggle::make('admin_password_require_lowercase')
                            ->label('Require Lowercase Letters')
                            ->default(true)
                            ->helperText('Admin passwords must contain at least one lowercase letter'),
                        Toggle::make('admin_password_require_numbers')
                            ->label('Require Numbers')
                            ->default(true)
                            ->helperText('Admin passwords must contain at least one number'),
                        Toggle::make('admin_password_require_symbols')
                            ->label('Require Special Characters')
                            ->default(true)
                            ->helperText('Admin passwords must contain at least one special character'),
                    ]);
                $components[] = Section::make('Admin Session Settings')
                    ->components([
                        TextInput::make('admin_session_lifetime')
                            ->label('Session Lifetime (minutes)')
                            ->numeric()
                            ->default(120)
                            ->minValue(1)
                            ->maxValue(525600)
                            ->required()
                            ->helperText('How long admin user sessions should remain active (1-525600 minutes)'),
                    ]);
                $components[] = Section::make('Admin Two-Factor Authentication')
                    ->components([
                        Toggle::make('admin_two_factor_enabled')
                            ->label('Enable Two-Factor Authentication')
                            ->default(false)
                            ->helperText('Allow admin users to enable 2FA for their accounts'),
                    ]);
            }
        } elseif ($this->activeTab === 'localization') {
            $components[] = Section::make('Localization Settings')
                ->components([
                    Select::make('default_language')
                        ->label('Default Language')
                        ->options([
                            'en' => 'English',
                        ])
                        ->default('en')
                        ->required()
                        ->disabled()
                        ->helperText('Default language for the application'),
                    Select::make('default_currency')
                        ->label('Default Currency')
                        ->options([
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'US Dollar (USD)',
                            'GBP' => 'British Pound (GBP)',
                            'JPY' => 'Japanese Yen (JPY)',
                            'CHF' => 'Swiss Franc (CHF)',
                            'CAD' => 'Canadian Dollar (CAD)',
                            'AUD' => 'Australian Dollar (AUD)',
                            'CNY' => 'Chinese Yuan (CNY)',
                            'INR' => 'Indian Rupee (INR)',
                            'BRL' => 'Brazilian Real (BRL)',
                            'RUB' => 'Russian Ruble (RUB)',
                            'ZAR' => 'South African Rand (ZAR)',
                            'SEK' => 'Swedish Krona (SEK)',
                            'NOK' => 'Norwegian Krone (NOK)',
                            'DKK' => 'Danish Krone (DKK)',
                            'PLN' => 'Polish Zloty (PLN)',
                            'CZK' => 'Czech Koruna (CZK)',
                            'HUF' => 'Hungarian Forint (HUF)',
                            'RON' => 'Romanian Leu (RON)',
                            'BGN' => 'Bulgarian Lev (BGN)',
                            'HRK' => 'Croatian Kuna (HRK)',
                            'TRY' => 'Turkish Lira (TRY)',
                        ])
                        ->default('EUR')
                        ->required()
                        ->searchable()
                        ->helperText('Default currency for the application'),
                    Select::make('default_timezone')
                        ->label('Default Timezone')
                        ->options($this->getTimezoneOptions())
                        ->default('Europe/Athens')
                        ->required()
                        ->searchable()
                        ->helperText('Default timezone for the application'),
                    Select::make('date_format')
                        ->label('Date Format')
                        ->options([
                            'Y-m-d' => 'Y-m-d (2024-01-15)',
                            'd/m/Y' => 'd/m/Y (15/01/2024)',
                            'm/d/Y' => 'm/d/Y (01/15/2024)',
                            'd.m.Y' => 'd.m.Y (15.01.2024)',
                            'd-m-Y' => 'd-m-Y (15-01-2024)',
                            'Y/m/d' => 'Y/m/d (2024/01/15)',
                            'l, F j, Y' => 'l, F j, Y (Monday, January 15, 2024)',
                            'F j, Y' => 'F j, Y (January 15, 2024)',
                            'j F Y' => 'j F Y (15 January 2024)',
                        ])
                        ->default('d/m/Y')
                        ->required()
                        ->helperText('Date format for displaying dates'),
                    Select::make('time_format')
                        ->label('Time Format')
                        ->options([
                            'H:i:s' => 'H:i:s (14:30:45)',
                            'H:i' => 'H:i (14:30)',
                            'h:i:s A' => 'h:i:s A (02:30:45 PM)',
                            'h:i A' => 'h:i A (02:30 PM)',
                            'G:i:s' => 'G:i:s (14:30:45 - 24h no leading zero)',
                            'g:i A' => 'g:i A (2:30 PM - 12h no leading zero)',
                        ])
                        ->default('H:i:s')
                        ->required()
                        ->helperText('Time format for displaying times'),
                ]);
        } elseif ($this->activeTab === 'store_location') {
            if ($this->activeStoreTab === 'main') {
                // Main store - only show Contact, Address, Coordinates (pre-filled from General, read-only)
                $components[] = Section::make('Contact Information')
                    ->description('Contact details for the main store (read-only, managed from General settings)')
                    ->components([
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter store phone number')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter store email address')
                            ->helperText('This field is managed from General Settings'),
                    ]);
                $components[] = Section::make('Address')
                    ->description('Physical address of the main store (read-only, managed from General settings)')
                    ->components([
                        TextInput::make('address_street')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter street address')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('address_city')
                            ->label('City')
                            ->maxLength(100)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter city')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('address_region')
                            ->label('Region/State')
                            ->maxLength(100)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter region/state')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('address_postal_code')
                            ->label('Postal Code')
                            ->maxLength(20)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter postal code')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('address_country')
                            ->label('Country')
                            ->maxLength(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter country code')
                            ->helperText('This field is managed from General Settings'),
                    ]);
                $components[] = Section::make('Coordinates')
                    ->description('Geographic coordinates for map integration (read-only, managed from General settings)')
                    ->components([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter latitude')
                            ->helperText('This field is managed from General Settings'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Enter longitude')
                            ->helperText('This field is managed from General Settings'),
                    ]);
            } elseif ($this->activeStoreTab !== 'main') {
                // New store or editing existing store - show all fields
                $isEditing = $this->selectedStoreId && $this->activeStoreTab === $this->selectedStoreId;
                $components[] = Section::make('Store Information')
                    ->description($isEditing ? 'Edit store information' : 'Basic information about your store')
                    ->components([
                        TextInput::make('store_name')
                            ->label('Store Name')
                            ->maxLength(255)
                            ->default($this->newStoreId && $this->activeStoreTab === $this->newStoreId ? $this->newStoreName : '')
                            ->placeholder('Enter store name')
                            ->helperText('The name of your store')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state) {
                                if ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
                                    $this->newStoreName = $state ?: 'NEW STORE';
                                }
                            }),
                    ]);
                $components[] = Section::make('Address')
                    ->description('Physical address of your store')
                    ->components([
                        TextInput::make('address_street')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->placeholder('Enter street address')
                            ->helperText('Street name and number'),
                        TextInput::make('address_city')
                            ->label('City')
                            ->maxLength(100)
                            ->placeholder('Enter city')
                            ->helperText('City name'),
                        TextInput::make('address_region')
                            ->label('Region/State')
                            ->maxLength(100)
                            ->placeholder('Enter region/state')
                            ->helperText('State, province, or region'),
                        TextInput::make('address_postal_code')
                            ->label('Postal Code')
                            ->maxLength(20)
                            ->placeholder('Enter postal code')
                            ->helperText('ZIP or postal code'),
                        TextInput::make('address_country')
                            ->label('Country')
                            ->maxLength(2)
                            ->default('GR')
                            ->placeholder('Enter country code')
                            ->helperText('ISO 3166-1 alpha-2 country code (e.g., GR, US, UK)'),
                    ]);
                $components[] = Section::make('Contact Information')
                    ->description('Contact details for your store')
                    ->components([
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50)
                            ->placeholder('Enter store phone number')
                            ->helperText('Store phone number'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Enter store email address')
                            ->helperText('Store email address'),
                    ]);
                $components[] = Section::make('Coordinates')
                    ->description('Geographic coordinates for map integration')
                    ->components([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->placeholder('Enter latitude')
                            ->helperText('Latitude coordinate (e.g., 37.9838)'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->maxLength(20)
                            ->placeholder('Enter longitude')
                            ->helperText('Longitude coordinate (e.g., 23.7275)'),
                    ]);
            }
        }

        return $schema
            ->components($components)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $updateService = app(UpdateSettingsService::class);

        if ($this->activeTab === 'store_location') {
            if ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
                // Save new store to JSON array
                $stores = $this->getStores();
                $storeId = uniqid('store_', true);
                $stores[$storeId] = [
                    'store_name' => $data['store_name'] ?? '',
                    'address_street' => $data['address_street'] ?? '',
                    'address_city' => $data['address_city'] ?? '',
                    'address_region' => $data['address_region'] ?? '',
                    'address_postal_code' => $data['address_postal_code'] ?? '',
                    'address_country' => $data['address_country'] ?? 'GR',
                    'phone' => $data['phone'] ?? '',
                    'email' => $data['email'] ?? '',
                    'latitude' => $data['latitude'] ?? '',
                    'longitude' => $data['longitude'] ?? '',
                ];
                $updateService->execute('stores', $stores, 'json', 'store_location');
                $this->newStoreId = null;
                $this->newStoreName = 'NEW STORE';
                $this->selectedStoreId = null;
                $this->activeStoreTab = 'main';
                $this->loadTabData();
                Notification::make()
                    ->title('Store Added')
                    ->success()
                    ->send();

                return;
            } elseif ($this->selectedStoreId && $this->activeStoreTab === $this->selectedStoreId) {
                // Update existing store
                $stores = $this->getStores();
                if (isset($stores[$this->selectedStoreId])) {
                    $stores[$this->selectedStoreId] = [
                        'store_name' => $data['store_name'] ?? '',
                        'address_street' => $data['address_street'] ?? '',
                        'address_city' => $data['address_city'] ?? '',
                        'address_region' => $data['address_region'] ?? '',
                        'address_postal_code' => $data['address_postal_code'] ?? '',
                        'address_country' => $data['address_country'] ?? 'GR',
                        'phone' => $data['phone'] ?? '',
                        'email' => $data['email'] ?? '',
                        'latitude' => $data['latitude'] ?? '',
                        'longitude' => $data['longitude'] ?? '',
                    ];
                    $updateService->execute('stores', $stores, 'json', 'store_location');
                    // Keep the store selected so the tab name updates
                    // Don't reset to main, just reload the data
                    $this->loadTabData();
                    Notification::make()
                        ->title('Store Updated')
                        ->success()
                        ->send();

                    return;
                }
            }
        }

        $group = $this->activeTab;
        $booleanFields = [
            'maintenance_mode',
            'enable_login_signup',
            'password_require_uppercase',
            'password_require_lowercase',
            'password_require_numbers',
            'password_require_symbols',
            'admin_password_require_uppercase',
            'admin_password_require_lowercase',
            'admin_password_require_numbers',
            'admin_password_require_symbols',
            'site_two_factor_enabled',
            'admin_two_factor_enabled',
        ];
        $integerFields = [
            'mail_port',
            'password_min_length',
            'admin_password_min_length',
            'site_session_lifetime',
            'admin_session_lifetime',
        ];
        $decimalFields = [
            'latitude',
            'longitude',
        ];

        foreach ($data as $key => $value) {
            $type = 'string';
            if (in_array($key, $booleanFields)) {
                $type = 'boolean';
            } elseif (in_array($key, $integerFields)) {
                $type = 'integer';
            } elseif (in_array($key, $decimalFields)) {
                $type = 'decimal';
            }

            $updateService->execute($key, $value, $type, $group);
        }

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return 'Settings';
    }

    /**
     * Get all stores from settings
     */
    public function getStores(): array
    {
        $getSettings = app(GetSettingsService::class);
        $storesJson = $getSettings->get('stores', '[]');

        if (is_string($storesJson)) {
            $stores = json_decode($storesJson, true);

            return is_array($stores) ? $stores : [];
        }

        return is_array($storesJson) ? $storesJson : [];
    }

    /**
     * Edit a store
     */
    public function editStore(string $storeId): void
    {
        $this->selectedStoreId = $storeId;
        $this->newStoreId = null;
        $this->activeStoreTab = $storeId;
        $this->loadTabData();
    }

    /**
     * Switch to main store tab (with check for unsaved new store)
     */
    public function switchToMainStore(): void
    {
        if ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
            Notification::make()
                ->title('Unsaved Changes')
                ->body('Please save or delete the new store before changing tabs.')
                ->warning()
                ->send();

            return;
        }
        $this->selectedStoreId = null;
        $this->newStoreId = null;
        $this->activeStoreTab = 'main';
        $this->loadTabData();
    }

    /**
     * Switch to a store tab (with check for unsaved new store)
     */
    public function switchToStore(string $storeId): void
    {
        if ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
            Notification::make()
                ->title('Unsaved Changes')
                ->body('Please save or delete the new store before changing tabs.')
                ->warning()
                ->send();

            return;
        }
        $this->editStore($storeId);
    }

    /**
     * Create new store tab
     */
    public function createNewStoreTab(): void
    {
        $this->newStoreId = 'new_store_'.uniqid();
        $this->selectedStoreId = null;
        $this->activeStoreTab = $this->newStoreId;
        $this->loadTabData();
    }

    /**
     * Delete a store
     */
    public function deleteStore(string $storeId): void
    {
        $stores = $this->getStores();
        if (isset($stores[$storeId])) {
            unset($stores[$storeId]);
            $updateService = app(UpdateSettingsService::class);
            $updateService->execute('stores', $stores, 'json', 'store_location');
            $this->selectedStoreId = null;
            $this->activeStoreTab = 'main';
            Notification::make()
                ->title('Store Deleted')
                ->success()
                ->send();
        }
    }

    /**
     * Delete current store (the one in active tab)
     */
    public function deleteCurrentStore(): void
    {
        if ($this->activeStoreTab === 'main') {
            return; // Cannot delete main store
        }

        if ($this->newStoreId && $this->activeStoreTab === $this->newStoreId) {
            // Just close the new store tab
            $this->newStoreId = null;
            $this->newStoreName = 'NEW STORE';
            $this->activeStoreTab = 'main';
            $this->loadTabData();
            Notification::make()
                ->title('New Store Cancelled')
                ->success()
                ->send();

            return;
        }

        if ($this->selectedStoreId && $this->activeStoreTab === $this->selectedStoreId) {
            // Delete the selected store
            $this->deleteStore($this->selectedStoreId);
        }
    }

    /**
     * Get timezone options grouped by region
     */
    private function getTimezoneOptions(): array
    {
        $timezones = timezone_identifiers_list();
        $grouped = [];

        foreach ($timezones as $timezone) {
            $parts = explode('/', $timezone);
            if (count($parts) === 2) {
                $region = $parts[0];
                $city = $parts[1];
                if (! isset($grouped[$region])) {
                    $grouped[$region] = [];
                }
                $grouped[$region][$timezone] = str_replace('_', ' ', $city).' ('.$timezone.')';
            } else {
                $grouped['Other'][$timezone] = $timezone;
            }
        }

        // Flatten and sort
        $options = [];
        ksort($grouped);
        foreach ($grouped as $region => $cities) {
            if ($region === 'Other') {
                foreach ($cities as $tz => $label) {
                    $options[$tz] = $label;
                }
            } else {
                foreach ($cities as $tz => $label) {
                    $options[$tz] = $label;
                }
            }
        }

        return $options;
    }
}
