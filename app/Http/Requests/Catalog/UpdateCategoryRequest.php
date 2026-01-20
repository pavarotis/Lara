<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;
        $businessId = $this->route('category')?->business_id;

        return [
            'name' => ValidationHelper::name(),
            'slug' => array_merge(
                ValidationHelper::slugOptional(),
                [
                    Rule::unique('categories')
                        ->where('business_id', $businessId)
                        ->ignore($categoryId),
                ]
            ),
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
