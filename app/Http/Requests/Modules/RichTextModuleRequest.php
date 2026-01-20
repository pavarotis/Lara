<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use App\Support\PermissionHelper;
use Illuminate\Foundation\Http\FormRequest;

class RichTextModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'alignment' => ['nullable', 'string', 'in:left,center,right,justify'],
        ];
    }
}
