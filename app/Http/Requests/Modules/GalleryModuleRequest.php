<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class GalleryModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required'],
            'columns' => ['nullable', 'integer', 'min:1', 'max:4'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Handle images array - can be array of IDs or array of objects with id
        if ($this->has('images')) {
            $images = $this->input('images');

            // If it's an array, normalize to array of IDs
            $normalizedImages = [];
            foreach ($images as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $normalizedImages[] = $item['id'];
                } elseif (is_numeric($item)) {
                    $normalizedImages[] = (int) $item;
                }
            }

            $this->merge(['images' => $normalizedImages]);
        }
    }
}
