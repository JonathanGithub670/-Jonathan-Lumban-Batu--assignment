<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CltLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'engineering_note' => 'nullable|string',
        ];
    }
}
