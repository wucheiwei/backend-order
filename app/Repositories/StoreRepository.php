<?php

namespace App\Repositories;

use App\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreRepository
{
    protected $model;

    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    /**
     * 取得所有 Store（分頁）
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('sort', 'asc')
            ->paginate($perPage);
    }

    /**
     * 根據 ID 查詢 Store
     *
     * @param int $id
     * @return Store|null
     */
    public function findById(int $id): ?Store
    {
        return $this->model->find($id);
    }

    /**
     * 取得最大的 sort 值
     *
     * @return int
     */
    public function getMaxSort(): int
    {
        $maxSort = $this->model->max('sort');
        return $maxSort ? (int)$maxSort : 0;
    }

    /**
     * 創建新 Store
     *
     * @param array $data
     * @return Store
     */
    public function create(array $data): Store
    {
        return $this->model->create($data);
    }

    /**
     * 更新 Store
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
     * 軟刪除 Store
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $store = $this->findById($id);
        if (!$store) {
            return false;
        }
        return $store->delete();
    }
}

