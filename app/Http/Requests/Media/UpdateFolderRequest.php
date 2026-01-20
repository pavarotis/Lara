<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use App\Domain\Media\Models\MediaFolder;
use App\Support\PermissionHelper;
use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionHelper::isAdmin($this->user());
    }

    public function rules(): array
    {
        $folder = $this->route('folder');
        $businessId = $folder instanceof MediaFolder ? $folder->business_id : $this->input('business_id');
        $parentId = $folder instanceof MediaFolder ? $folder->parent_id : $this->input('parent_id');

        return [
            'name' => array_merge(
                ValidationHelper::name(),
                [
                    Rule::unique('media_folders', 'name')
                        ->where('business_id', $businessId)
                        ->where('parent_id', $parentId)
                        ->ignore($folder instanceof MediaFolder ? $folder->id : null),
                ]
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Το όνομα φακέλου είναι υποχρεωτικό.',
            'name.unique' => 'Υπάρχει ήδη φάκελος με αυτό το όνομα σε αυτό το επίπεδο.',
        ];
    }
}
