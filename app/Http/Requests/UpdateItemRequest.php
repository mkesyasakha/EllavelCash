<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => ['required','string','max:255', Rule::unique('items', 'name')->ignore($this->item->id)],    
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0|max:999999.99',
            'stock' => 'sometimes|required|integer|min:0',
        ];
    }
}
