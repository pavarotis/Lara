<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use App\Support\PermissionHelper;
use Illuminate\Foundation\Http\FormRequest;

class FaqModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'faqs' => ['required', 'array', 'min:1'],
            'faqs.*.question' => ['required', 'string', 'max:500'],
            'faqs.*.answer' => ['required', 'string'],
        ];
    }
}
