<?php

namespace App\Services;

use App\Repositories\StoreRepository;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreService
{
    protected $storeRepository;
    protected $productRepository;

    public function __construct(StoreRepository $storeRepository, ProductRepository $productRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * 取得所有 Store（分頁）
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->storeRepository->getAll($perPage);
    }

    /**
     * 根據 ID 取得 Store
     *
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getById(int $id): array
    {
        $store = $this->storeRepository->findById($id);

        if (!$store) {
            throw new \Exception('類別不存在', 404);
        }

        return [
            'id' => $store->id,
            'name' => $store->name,
            'sort' => $store->sort,
            'created_at' => $store->created_at,
            'updated_at' => $store->updated_at,
        ];
    }

    /**
     * 批量創建新 Store
     *
     * @param array $storesData
     * @return array
     * @throws \Exception
     */
    public function createBatch(array $storesData): array
    {
        $results = [];
        $maxSort = $this->storeRepository->getMaxSort();

        foreach ($storesData as $data) {
            // 如果沒有傳入 sort，則設定為當前最大值 + 1
            if (!isset($data['sort'])) {
                $maxSort++;
                $data['sort'] = $maxSort;
            } else {
                // 如果傳入了 sort，更新 maxSort 以確保後續的 sort 不會衝突
                $maxSort = max($maxSort, $data['sort']);
            }

            $store = $this->storeRepository->create($data);

            $results[] = [
                'id' => $store->id,
                'name' => $store->name,
                'sort' => $store->sort,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
            ];
        }

        return $results;
    }

    /**
     * 批量更新 Store
     *
     * @param array $storesData
     * @return array
     * @throws \Exception
     */
    public function updateBatch(array $storesData): array
    {
        $results = [];

        foreach ($storesData as $item) {
            $id = $item['id'];

            $store = $this->storeRepository->findById($id);

            if (!$store) {
                throw new \Exception("類別 ID {$id} 不存在", 404);
            }

            // update 方法會自動從 data 中取出 id
            $updated = $this->storeRepository->update($item);

            if (!$updated) {
                throw new \Exception("更新類別 ID {$id} 失敗", 500);
            }

            // 重新取得更新後的資料
            $store->refresh();

            $results[] = [
                'id' => $store->id,
                'name' => $store->name,
                'sort' => $store->sort,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
            ];
        }

        return $results;
    }

    /**
     * 刪除 Store（軟刪除），同時軟刪除所有相關的 Products
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $store = $this->storeRepository->findById($id);

        if (!$store) {
            throw new \Exception('類別不存在', 404);
        }

        // 先軟刪除所有相關的 products（findByStoreId 已排除軟刪除的記錄）
        $products = $this->productRepository->findByStoreId($id);
        foreach ($products as $product) {
            $this->productRepository->delete($product->id);
        }

        // 再軟刪除 store
        $deleted = $this->storeRepository->delete($id);

        if (!$deleted) {
            throw new \Exception('刪除失敗', 500);
        }
    }
}

