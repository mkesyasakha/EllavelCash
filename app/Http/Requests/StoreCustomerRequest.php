<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Update this to apply authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->route('user'), // Ignore the current user's email
            'phone' => 'required|unique:users,phone,' . $this->route('user'), // Ignore the current user's phone number
            'password' => 'nullable|string|min:8|confirmed', // Password is optional, but if provided, must meet the criteria
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional photo upload
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email address is already taken.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
            'photo.image' => 'The photo must be an image file.',
            'photo.max' => 'The photo file size must not exceed 2MB.',
        ];
    }
}
