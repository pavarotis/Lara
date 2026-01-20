<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use App\Support\PermissionHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required_without:files',
                File::types(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'video/mp4', 'video/mpeg', 'application/pdf'])
                    ->max(10240), // 10MB max
            ],
            'files' => [
                'required_without:file',
                'array',
            ],
            'files.*' => [
                'required',
                File::types(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'video/mp4', 'video/mpeg', 'application/pdf'])
                    ->max(10240), // 10MB max
            ],
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required_without' => 'Το αρχείο είναι υποχρεωτικό.',
            'files.required_without' => 'Τα αρχεία είναι υποχρεωτικά.',
            'file.mimes' => 'Το αρχείο πρέπει να είναι εικόνα, video ή PDF.',
            'files.*.mimes' => 'Κάθε αρχείο πρέπει να είναι εικόνα, video ή PDF.',
            'file.max' => 'Το αρχείο δεν μπορεί να είναι μεγαλύτερο από 10MB.',
            'files.*.max' => 'Κάθε αρχείο δεν μπορεί να είναι μεγαλύτερο από 10MB.',
            'folder_id.exists' => 'Ο φάκελος δεν υπάρχει.',
        ];
    }
}
