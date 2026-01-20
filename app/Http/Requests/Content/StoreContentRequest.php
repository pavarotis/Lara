<?php

declare(strict_types=1);

namespace App\Http\Requests\Content;

use App\Support\ContentStatusHelper;
use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        $businessId = $this->input('business_id') ?? $this->user()?->business_id;

        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'type' => ['required', 'string', Rule::in(['page', 'article', 'block'])],
            'title' => ValidationHelper::name(),
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('contents', 'slug')->where('business_id', $businessId),
            ],
            'body_json' => [
                'required_without:blocks',
                'array',
            ],
            'body_json.*.type' => ['required_with:body_json', 'string'],
            'body_json.*.props' => ['required_with:body_json', 'array'],
            'blocks' => ['required_without:body_json', 'array'], // Alternative input format
            'blocks.*.type' => ['required_with:blocks', 'string'],
            'blocks.*.props' => ['required_with:blocks', 'array'],
            'meta' => ['nullable', 'array'],
            'status' => ContentStatusHelper::nullableValidationRule(),
        ];
    }

    public function messages(): array
    {
        return [
            'business_id.required' => 'Το business είναι υποχρεωτικό.',
            'type.required' => 'Ο τύπος περιεχομένου είναι υποχρεωτικός.',
            'type.in' => 'Ο τύπος περιεχομένου πρέπει να είναι: page, article, ή block.',
            'title.required' => 'Ο τίτλος είναι υποχρεωτικός.',
            'slug.unique' => 'Αυτό το slug χρησιμοποιείται ήδη για αυτό το business.',
            'body_json.required_without' => 'Το περιεχόμενο (blocks) είναι υποχρεωτικό.',
            'body_json.array' => 'Το περιεχόμενο πρέπει να είναι array.',
            'blocks.required_without' => 'Πρέπει να προσθέσετε τουλάχιστον ένα block.',
            'blocks.array' => 'Τα blocks πρέπει να είναι array.',
        ];
    }
}
