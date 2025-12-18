<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class ImageModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'image_id' => ['required', 'integer', 'exists:media,id'],
            'caption' => ['nullable', 'string', 'max:500'],
            'alignment' => ['nullable', 'string', 'in:left,center,right'],
        ];
    }
}
