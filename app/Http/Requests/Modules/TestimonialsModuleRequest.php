<?php

declare(strict_types=1);

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialsModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'testimonials' => ['required', 'array', 'min:1'],
            'testimonials.*.name' => ['required', 'string', 'max:255'],
            'testimonials.*.text' => ['required', 'string', 'max:1000'],
            'testimonials.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'testimonials.*.image_id' => ['nullable', 'integer', 'exists:media,id'],
            'columns' => ['nullable', 'integer', 'min:1', 'max:4'],
        ];
    }
}
