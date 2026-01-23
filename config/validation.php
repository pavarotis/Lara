<?php

/**
 * Validation Configuration
 *
 * Defines reusable validation rules for common fields.
 * Use ValidationHelper to access these rules.
 */

use Illuminate\Validation\Rules;

return [
    /**
     * Common Validation Rules
     * These rules can be reused across FormRequests
     */
    'rules' => [
        'email' => ['required', 'string', 'email', 'max:255'],
        'email_unique' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'name' => ['required', 'string', 'max:255'],
        // Note: Password rule cannot be cached in config (non-serializable)
        // Use Rules\Password::defaults() directly in FormRequests instead
        'password' => ['required', 'confirmed'],
        'password_current' => ['required', 'current_password'],
        'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
        'slug_optional' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
        'status' => ['sometimes', 'string'], // Will be merged with ContentStatusHelper
        'status_nullable' => ['nullable', 'string'], // Will be merged with ContentStatusHelper
        'boolean' => ['sometimes', 'boolean'],
        'boolean_required' => ['required', 'boolean'],
        'price' => ['required', 'numeric', 'min:0'],
        'price_optional' => ['nullable', 'numeric', 'min:0'],
        'sort_order' => ['nullable', 'integer', 'min:0'],
        'description' => ['nullable', 'string'],
        'description_required' => ['required', 'string'],
    ],
];
