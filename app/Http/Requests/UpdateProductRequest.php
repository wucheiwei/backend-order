<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'store_id' => [
                'required',
                'integer',
                Rule::exists('stores', 'id')->whereNull('deleted_at'), // 驗證 store_id 存在且未軟刪除
            ],
            'products' => 'required|array',
            // 有 id -> 更新；沒有 id -> 視為新增（在 withValidator() 再補必填判斷）
            'products.*.id' => 'sometimes|integer|exists:products,id',
            'products.*.name' => 'sometimes|string|max:255',
            'products.*.price' => 'sometimes|integer|min:0',
            'products.*.sort' => 'sometimes|integer|min:1',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $products = $this->input('products', []);
            $storeId = $this->input('store_id');
            
            if (!is_array($products)) {
                return;
            }

            foreach ($products as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $hasId = array_key_exists('id', $item) && $item['id'] !== null && $item['id'] !== '';

                // 驗證更新時，product 必須屬於該 store_id
                if ($hasId) {
                    $product = \App\Models\Product::find($item['id']);
                    if ($product && $product->store_id != $storeId) {
                        $validator->errors()->add("products.{$index}.id", "品項 ID {$item['id']} 不屬於類別 ID {$storeId}");
                    }
                }

                // 沒有 id：視為新增 -> name / price 必填（store_id 使用外層的）
                if (!$hasId) {
                    if (!array_key_exists('name', $item) || $item['name'] === null || $item['name'] === '') {
                        $validator->errors()->add("products.{$index}.name", '名稱為必填欄位');
                    }
                    if (!array_key_exists('price', $item) || $item['price'] === null || $item['price'] === '') {
                        $validator->errors()->add("products.{$index}.price", '金額為必填欄位');
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'store_id.required' => '類別 ID 為必填欄位',
            'store_id.integer' => '類別 ID 必須為整數',
            'store_id.exists' => '類別不存在',
            'products.required' => 'products 為必填欄位',
            'products.array' => 'products 必須為陣列',
            'products.*.id.integer' => 'id 必須為整數',
            'products.*.id.exists' => '品項不存在',
            'products.*.name.string' => '名稱必須為字串',
            'products.*.name.max' => '名稱長度不能超過 255 個字元',
            'products.*.price.integer' => '金額必須為整數',
            'products.*.price.min' => '金額必須大於或等於 0',
            'products.*.sort.integer' => '排序必須為整數',
            'products.*.sort.min' => '排序必須大於 0',
        ];
    }
}

