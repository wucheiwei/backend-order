<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * 取得所有 Product（分頁，包含關聯的 Store）
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->with('store')
            ->orderBy('id', 'asc')
            ->paginate($perPage);
    }

    /**
     * 根據 ID 查詢 Product（包含關聯的 Store）
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return $this->model->with('store')->find($id);
    }

    /**
     * 根據 Store ID 查詢 Products
     *
     * @param int $storeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByStoreId(int $storeId)
    {
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('sort', 'asc')
            ->get();
    }

    /**
     * 取得指定 Store 的最大 sort 值（排除軟刪除的記錄）
     *
     * @param int $storeId
     * @return int
     */
    public function getMaxSortByStoreId(int $storeId): int
    {
        $maxSort = $this->model
            ->where('store_id', $storeId)
            ->whereNull('deleted_at') // 排除軟刪除的記錄
            ->max('sort');
        return $maxSort ? (int)$maxSort : 0;
    }

    /**
     * 創建新 Product
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    /**
     * 更新 Product
     *
     * @param array $data 必須包含 id 欄位
     * @return bool
     */
    public function update(array $data): bool
    {
        if (!isset($data['id'])) {
            return false;
        }
        
        $id = $data['id'];
        unset($data['id']); // 移除 id，只保留要更新的欄位
        
        return $this->model->where('id', $id)->update($data) > 0;
    }

    /**
     * 軟刪除 Product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        if (!$product) {
            return false;
        }
        return $product->delete();
    }
}

