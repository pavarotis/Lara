<?php

declare(strict_types=1);

namespace App\Domain\Businesses\DTOs;

/**
 * Data Transfer Object for Business Settings
 * Documents the expected structure of business settings JSON
 */
class BusinessSettingsDTO
{
    public function __construct(
        // Ordering
        public bool $delivery_enabled = false,
        public bool $pickup_enabled = true,
        public float $minimum_order = 0,
        public ?string $delivery_fee = null,

        // Display
        public bool $show_catalog_images = true,
        public string $color_theme = 'default',

        // Currency & Tax
        public string $currency = 'EUR',
        public string $currency_symbol = '€',
        public float $tax_rate = 0.24,

        // Operating Hours (optional)
        public ?array $operating_hours = null,

        // Contact
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,

        // Social
        public ?string $facebook = null,
        public ?string $instagram = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            delivery_enabled: $data['delivery_enabled'] ?? false,
            pickup_enabled: $data['pickup_enabled'] ?? true,
            minimum_order: (float) ($data['minimum_order'] ?? 0),
            delivery_fee: $data['delivery_fee'] ?? null,
            show_catalog_images: $data['show_catalog_images'] ?? true,
            color_theme: $data['color_theme'] ?? 'default',
            currency: $data['currency'] ?? 'EUR',
            currency_symbol: $data['currency_symbol'] ?? '€',
            tax_rate: (float) ($data['tax_rate'] ?? 0.24),
            operating_hours: $data['operating_hours'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            facebook: $data['facebook'] ?? null,
            instagram: $data['instagram'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'delivery_enabled' => $this->delivery_enabled,
            'pickup_enabled' => $this->pickup_enabled,
            'minimum_order' => $this->minimum_order,
            'delivery_fee' => $this->delivery_fee,
            'show_catalog_images' => $this->show_catalog_images,
            'color_theme' => $this->color_theme,
            'currency' => $this->currency,
            'currency_symbol' => $this->currency_symbol,
            'tax_rate' => $this->tax_rate,
            'operating_hours' => $this->operating_hours,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
        ];
    }
}
