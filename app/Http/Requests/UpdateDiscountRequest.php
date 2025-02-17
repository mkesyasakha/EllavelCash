<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jika perlu otorisasi khusus
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:discounts,code,' . $this->discount->id . '|max:50',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'valid_until' => 'required|date|after_or_equal:today',
            'status' => 'required|in:active,expired',
        ];
    }
}
