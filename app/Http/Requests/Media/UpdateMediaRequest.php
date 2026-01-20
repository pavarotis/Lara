<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'name' => array_merge(['sometimes'], ValidationHelper::name()),
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Το όνομα πρέπει να είναι κείμενο.',
            'name.max' => 'Το όνομα δεν μπορεί να είναι μεγαλύτερο από 255 χαρακτήρες.',
            'folder_id.exists' => 'Ο φάκελος δεν υπάρχει.',
        ];
    }
}
