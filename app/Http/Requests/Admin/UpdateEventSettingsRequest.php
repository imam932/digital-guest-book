<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', 'max:255'],
            'event_description' => ['nullable', 'string', 'max:2000'],
            'main_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'form_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
