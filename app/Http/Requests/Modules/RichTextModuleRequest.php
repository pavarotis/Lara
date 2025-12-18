<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class RichTextModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'alignment' => ['nullable', 'string', 'in:left,center,right,justify'],
        ];
    }
}
