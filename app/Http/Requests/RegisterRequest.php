<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => '姓名為必填欄位',
            'name.string' => '姓名必須為字串',
            'name.max' => '姓名長度不能超過 255 個字元',
            'email.required' => 'Email 為必填欄位',
            'email.email' => 'Email 格式不正確',
            'email.unique' => '此 Email 已被使用',
            'password.required' => '密碼為必填欄位',
            'password.string' => '密碼必須為字串',
            'password.min' => '密碼長度至少為 8 個字元',
        ];
    }
}

