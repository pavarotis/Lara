<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class HeroModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image_id' => ['nullable', 'integer', 'exists:media,id'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_link' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }
}
