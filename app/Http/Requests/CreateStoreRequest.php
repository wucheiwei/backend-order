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
            'stores.*.products' => 'sometimes|array',
            'stores.*.products.*.name' => 'required|string|max:255',
            'stores.*.products.*.price' => 'required|integer|min:0',
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
            'stores.*.products.array' => 'products 必須為陣列',
            'stores.*.products.*.name.required_with' => '品項名稱為必填欄位',
            'stores.*.products.*.name.string' => '品項名稱必須為字串',
            'stores.*.products.*.name.max' => '品項名稱長度不能超過 255 個字元',
            'stores.*.products.*.price.required_with' => '品項金額為必填欄位',
            'stores.*.products.*.price.integer' => '品項金額必須為整數',
            'stores.*.products.*.price.min' => '品項金額必須大於或等於 0',
        ];
    }
}

