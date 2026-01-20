<?php

namespace App\Http\Requests;

use App\Support\ValidationHelper;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ValidationHelper::name(),
            'email' => ValidationHelper::emailUnique($this->user()->id),
        ];
    }
}
