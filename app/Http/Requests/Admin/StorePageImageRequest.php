<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePageImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('admin manage page images');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // 5MB
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'image.required' => 'Bitte wählen Sie ein Bild aus.',
            'image.image' => 'Die Datei muss ein Bild sein.',
            'image.mimes' => 'Das Bild muss vom Typ JPG, JPEG, PNG, GIF oder WebP sein.',
            'image.max' => 'Das Bild darf maximal 5 MB groß sein.',
        ];
    }
}
