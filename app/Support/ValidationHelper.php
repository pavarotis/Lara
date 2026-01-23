<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Validation Helper
 *
 * Provides reusable validation rules for common fields.
 * Use this class to ensure consistent validation across FormRequests.
 */
class ValidationHelper
{
    /**
     * Email validation rule
     */
    public static function email(): array
    {
        return config('validation.rules.email', ['required', 'string', 'email', 'max:255']);
    }

    /**
     * Email validation rule with unique constraint
     */
    public static function emailUnique(?int $ignoreUserId = null): array
    {
        $rule = ['required', 'string', 'email', 'max:255'];

        if ($ignoreUserId) {
            $rule[] = Rule::unique(User::class)->ignore($ignoreUserId);
        } else {
            $rule[] = Rule::unique(User::class);
        }

        return $rule;
    }

    /**
     * Name validation rule
     */
    public static function name(): array
    {
        return config('validation.rules.name', ['required', 'string', 'max:255']);
    }

    /**
     * Password validation rule
     *
     * Note: Password rule cannot be cached in config (non-serializable).
     * Always adds Password::defaults() directly here.
     */
    public static function password(): array
    {
        $baseRules = config('validation.rules.password', ['required', 'confirmed']);
        // Always add Password rule directly (cannot be in config cache)
        $baseRules[] = \Illuminate\Validation\Rules\Password::defaults();

        return $baseRules;
    }

    /**
     * Current password validation rule
     */
    public static function passwordCurrent(): array
    {
        return config('validation.rules.password_current', ['required', 'current_password']);
    }

    /**
     * Slug validation rule
     */
    public static function slug(): array
    {
        return config('validation.rules.slug', ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/']);
    }

    /**
     * Optional slug validation rule
     */
    public static function slugOptional(): array
    {
        return config('validation.rules.slug_optional', ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/']);
    }

    /**
     * Content status validation rule
     */
    public static function status(): array
    {
        return ContentStatusHelper::validationRule();
    }

    /**
     * Optional content status validation rule
     */
    public static function statusOptional(): array
    {
        return ContentStatusHelper::nullableValidationRule();
    }

    /**
     * Boolean validation rule
     */
    public static function boolean(): array
    {
        return config('validation.rules.boolean', ['sometimes', 'boolean']);
    }

    /**
     * Required boolean validation rule
     */
    public static function booleanRequired(): array
    {
        return config('validation.rules.boolean_required', ['required', 'boolean']);
    }

    /**
     * Price validation rule
     */
    public static function price(): array
    {
        return config('validation.rules.price', ['required', 'numeric', 'min:0']);
    }

    /**
     * Optional price validation rule
     */
    public static function priceOptional(): array
    {
        return config('validation.rules.price_optional', ['nullable', 'numeric', 'min:0']);
    }

    /**
     * Sort order validation rule
     */
    public static function sortOrder(): array
    {
        return config('validation.rules.sort_order', ['nullable', 'integer', 'min:0']);
    }

    /**
     * Description validation rule
     */
    public static function description(): array
    {
        return config('validation.rules.description', ['nullable', 'string']);
    }

    /**
     * Required description validation rule
     */
    public static function descriptionRequired(): array
    {
        return config('validation.rules.description_required', ['required', 'string']);
    }
}
