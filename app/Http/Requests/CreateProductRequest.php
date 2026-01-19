<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.store_id' => [
                'required',
                'integer',
                Rule::exists('stores', 'id')->whereNull('deleted_at'), // 驗證 store_id 存在且未軟刪除
            ],
            'products.*.name' => 'required|string|max:255',
            'products.*.price' => 'required|integer|min:0',
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
            'products.required' => 'products 為必填欄位',
            'products.array' => 'products 必須為陣列',
            'products.*.store_id.required' => '類別 ID 為必填欄位',
            'products.*.store_id.integer' => '類別 ID 必須為整數',
            'products.*.store_id.exists' => '類別不存在',
            'products.*.name.required' => '名稱為必填欄位',
            'products.*.name.string' => '名稱必須為字串',
            'products.*.name.max' => '名稱長度不能超過 255 個字元',
            'products.*.price.required' => '金額為必填欄位',
            'products.*.price.integer' => '金額必須為整數',
            'products.*.price.min' => '金額必須大於或等於 0',
        ];
    }
}

