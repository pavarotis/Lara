<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'name' => ValidationHelper::name(),
            'slug' => ValidationHelper::slugOptional(),
            'description' => ValidationHelper::description(),
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ValidationHelper::boolean(),
            'sort_order' => ValidationHelper::sortOrder(),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Το όνομα κατηγορίας είναι υποχρεωτικό.',
        ];
    }
}
