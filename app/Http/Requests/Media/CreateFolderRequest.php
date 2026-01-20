<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        $businessId = $this->input('business_id') ?? $this->user()?->business_id;
        $parentId = $this->input('parent_id');

        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'name' => array_merge(
                ValidationHelper::name(),
                [
                    Rule::unique('media_folders', 'name')
                        ->where('business_id', $businessId)
                        ->where('parent_id', $parentId),
                ]
            ),
            'parent_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_id.required' => 'Το business είναι υποχρεωτικό.',
            'name.required' => 'Το όνομα φακέλου είναι υποχρεωτικό.',
            'name.unique' => 'Υπάρχει ήδη φάκελος με αυτό το όνομα σε αυτό το επίπεδο.',
            'parent_id.exists' => 'Ο γονικός φάκελος δεν υπάρχει.',
        ];
    }
}
