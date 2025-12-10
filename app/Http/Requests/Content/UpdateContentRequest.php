<?php

declare(strict_types=1);

namespace App\Http\Requests\Content;

use App\Domain\Content\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        $content = $this->route('content');
        $businessId = $content instanceof Content ? $content->business_id : $this->input('business_id');

        return [
            'type' => ['sometimes', 'string', Rule::in(['page', 'article', 'block'])],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('contents', 'slug')
                    ->where('business_id', $businessId)
                    ->ignore($content instanceof Content ? $content->id : null),
            ],
            'body_json' => [
                'sometimes',
                'required_without:blocks',
                'array',
            ],
            'body_json.*.type' => ['required_with:body_json', 'string'],
            'body_json.*.props' => ['required_with:body_json', 'array'],
            'blocks' => ['sometimes', 'required_without:body_json', 'array'], // Alternative input format
            'blocks.*.type' => ['required_with:blocks', 'string'],
            'blocks.*.props' => ['required_with:blocks', 'array'],
            'meta' => ['nullable', 'array'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Ο τύπος περιεχομένου πρέπει να είναι: page, article, ή block.',
            'title.required' => 'Ο τίτλος είναι υποχρεωτικός.',
            'slug.unique' => 'Αυτό το slug χρησιμοποιείται ήδη για αυτό το business.',
            'body_json.required' => 'Το περιεχόμενο (blocks) είναι υποχρεωτικό.',
            'body_json.array' => 'Το περιεχόμενο πρέπει να είναι array.',
        ];
    }
}
