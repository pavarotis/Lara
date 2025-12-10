<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
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
