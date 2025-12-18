<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class ProductsGridModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['required', 'integer', 'exists:categories,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'columns' => ['nullable', 'integer', 'min:1', 'max:6'],
        ];
    }
}
