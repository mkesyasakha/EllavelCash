<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan true agar request bisa diproses
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'description' => 'required',
            'transaction_date' => 'required|date|after_or_equal:today',
            'items' => 'required|array',
            'items.*' => 'exists:items,id',
            'quantities' => 'required|array',
        ];
    }
}
