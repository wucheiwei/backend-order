<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSingleProductRequest extends FormRequest
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
        $productId = $this->route('id'); // 從路由參數取得 id

        return [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'store_id' => [
                'sometimes',
                'integer',
                Rule::exists('stores', 'id')->whereNull('deleted_at'), // 驗證 store_id 存在且未軟刪除
            ],
            'sort' => 'sometimes|integer|min:1',
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
            'name.string' => '名稱必須為字串',
            'name.max' => '名稱長度不能超過 255 個字元',
            'price.integer' => '金額必須為整數',
            'price.min' => '金額必須大於或等於 0',
            'store_id.integer' => '類別 ID 必須為整數',
            'store_id.exists' => '類別不存在',
            'sort.integer' => '排序必須為整數',
            'sort.min' => '排序必須大於 0',
        ];
    }
}

