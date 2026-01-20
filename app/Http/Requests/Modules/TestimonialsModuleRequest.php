<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;

class TestimonialsModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'title' => array_merge(['nullable'], ValidationHelper::name()),
            'testimonials' => ['required', 'array', 'min:1'],
            'testimonials.*.name' => ValidationHelper::name(),
            'testimonials.*.text' => ['required', 'string', 'max:1000'],
            'testimonials.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'testimonials.*.image_id' => ['nullable', 'integer', 'exists:media,id'],
            'columns' => ['nullable', 'integer', 'min:1', 'max:4'],
        ];
    }
}
