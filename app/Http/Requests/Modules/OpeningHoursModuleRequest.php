<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class OpeningHoursModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'array'],
            'hours.*.open' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'hours.*.close' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'hours.*.closed' => ['nullable', 'boolean'],
        ];
    }
}
