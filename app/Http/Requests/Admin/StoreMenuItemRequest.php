<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('admin manage menus');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'menu_location' => ['required', 'string', 'in:header,footer'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id', 'required_without:url'],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255', 'required_without:page_id'],
            'target' => ['required', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Die Bezeichnung ist erforderlich.',
            'menu_location.required' => 'Die Menü-Location ist erforderlich.',
            'menu_location.in' => 'Die Menü-Location muss "header" oder "footer" sein.',
            'page_id.required_without' => 'Entweder eine Seite oder eine URL muss angegeben werden.',
            'url.required_without' => 'Entweder eine URL oder eine Seite muss angegeben werden.',
            'target.in' => 'Das Ziel muss "_self" oder "_blank" sein.',
        ];
    }
}
