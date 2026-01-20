<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Domain\Catalog\Services\ImageUploadService;
use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ValidationHelper::name(),
            'slug' => array_merge(
                ValidationHelper::slugOptional(),
                [Rule::unique('products')->ignore($productId)]
            ),
            'description' => ValidationHelper::description(),
            'price' => ValidationHelper::price(),
            'image' => ImageUploadService::getValidationRules(false),
            'is_available' => ValidationHelper::boolean(),
            'is_featured' => ValidationHelper::boolean(),
            'sort_order' => ValidationHelper::sortOrder(),
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Επιλέξτε κατηγορία.',
            'name.required' => 'Το όνομα είναι υποχρεωτικό.',
            'price.required' => 'Η τιμή είναι υποχρεωτική.',
            'price.min' => 'Η τιμή δεν μπορεί να είναι αρνητική.',
        ];
    }
}
