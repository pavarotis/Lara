<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use App\Support\PermissionHelper;
use Illuminate\Foundation\Http\FormRequest;

class CtaModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'text' => ['nullable', 'string', 'max:1000'],
            'button_text' => ['required', 'string', 'max:100'],
            'button_link' => ['required', 'string', 'url', 'max:500'],
            'button_style' => ['nullable', 'string', 'in:primary,secondary,outline'],
            'alignment' => ['nullable', 'string', 'in:left,center,right'],
        ];
    }
}
