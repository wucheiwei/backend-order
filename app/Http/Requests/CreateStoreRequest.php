<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreRequest extends FormRequest
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
            'stores' => 'required|array',
            'stores.*.name' => 'required|string|max:255',
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
            'stores.required' => 'stores 為必填欄位',
            'stores.array' => 'stores 必須為陣列',
            'stores.*.name.required' => '名稱為必填欄位',
            'stores.*.name.string' => '名稱必須為字串',
            'stores.*.name.max' => '名稱長度不能超過 255 個字元',
        ];
    }
}

