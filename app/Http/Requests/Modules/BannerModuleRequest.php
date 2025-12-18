<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class BannerModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string', 'max:1000'],
            'link' => ['nullable', 'string', 'url', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'background_image_id' => ['nullable', 'integer', 'exists:media,id'],
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }
}
