<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use App\Support\PermissionHelper;
use Illuminate\Foundation\Http\FormRequest;

class MapModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'zoom' => ['nullable', 'integer', 'min:1', 'max:20'],
            'height' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Require either address or both latitude and longitude
            if (empty($this->input('address')) && (empty($this->input('latitude')) || empty($this->input('longitude')))) {
                $validator->errors()->add('address', 'Either address or both latitude and longitude must be provided.');
            }
        });
    }
}
