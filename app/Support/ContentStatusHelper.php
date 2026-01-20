<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Validation\Rule;

/**
 * Content Status Helper
 *
 * Provides helper methods for content status validation and management.
 * Use this class to ensure consistent status handling across the application.
 */
class ContentStatusHelper
{
    /**
     * Get all valid status values
     */
    public static function all(): array
    {
        return array_values(config('content.statuses', ['draft', 'published', 'archived']));
    }

    /**
     * Get default status
     */
    public static function default(): string
    {
        return config('content.default_status', 'draft');
    }

    /**
     * Check if a status is valid
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::all(), true);
    }

    /**
     * Get validation rule for status field
     *
     * Usage:
     * 'status' => ContentStatusHelper::validationRule()
     */
    public static function validationRule(): array
    {
        return ['sometimes', 'string', Rule::in(self::all())];
    }

    /**
     * Get validation rule for nullable status field
     */
    public static function nullableValidationRule(): array
    {
        return ['nullable', 'string', Rule::in(self::all())];
    }

    /**
     * Validate a status value
     * Throws exception if invalid
     *
     * @throws \InvalidArgumentException
     */
    public static function validate(string $status): void
    {
        if (! self::isValid($status)) {
            throw new \InvalidArgumentException(
                "Invalid status '{$status}'. Valid statuses: ".implode(', ', self::all())
            );
        }
    }

    /**
     * Get all content types
     */
    public static function types(): array
    {
        return array_values(config('content.types', ['page', 'article', 'post']));
    }

    /**
     * Get default content type
     */
    public static function defaultType(): string
    {
        return config('content.default_type', 'page');
    }

    /**
     * Check if a content type is valid
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::types(), true);
    }

    /**
     * Get draft status value
     */
    public static function draft(): string
    {
        return config('content.statuses.draft', 'draft');
    }

    /**
     * Get published status value
     */
    public static function published(): string
    {
        return config('content.statuses.published', 'published');
    }

    /**
     * Get archived status value
     */
    public static function archived(): string
    {
        return config('content.statuses.archived', 'archived');
    }
}
