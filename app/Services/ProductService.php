<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    protected $productRepository;
    protected $storeRepository;

    public function __construct(
        ProductRepository $productRepository,
        StoreRepository $storeRepository
    ) {
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
    }

    /**
     * 取得所有 Product（分頁，包含關聯的 Store）
     *
     * @param int $perPage
     * @return array
     */
    public function getAll(int $perPage = 10): array
    {
        $products = $this->productRepository->getAll($perPage);
        $items = [];
        foreach ($products->items() as $product) {
            $store = $product->store;
            $items[] = [
                'store' => $store ? [
                    'id' => $store->id,
                    'name' => $store->name,
                    'sort' => $store->sort,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ] : null,
                'product' => [
                    'id' => $product->id,
                    'store_id' => $product->store_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'sort' => $product->sort,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ],
            ];
        }

        return [
            'products' => $items,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ];
    }

    /**
     * 根據 Store ID 取得所有 Products（不包含關聯的 Store）
     *
     * @param int $storeId
     * @return array
     * @throws \Exception
     */
    public function getByStoreId(int $storeId): array
    {
        // 驗證 store 是否存在
        $store = \App\Models\Store::find($storeId);
        if (!$store) {
            throw new \Exception('類別不存在', 404);
        }

        $products = $this->productRepository->findByStoreId($storeId);
        
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'id' => $product->id,
                'store_id' => $product->store_id,
                'name' => $product->name,
                'price' => $product->price,
                'sort' => $product->sort,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        }

        return [
            'products' => $items,
        ];
    }

    /**
     * 根據 ID 取得 Product（包含關聯的 Store）
     *
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getById(int $id): array
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new \Exception('品項不存在', 404);
        }

        $store = $product->store;

        return [
            'products' => [
                [
                    'store' => $store ? [
                        'id' => $store->id,
                        'name' => $store->name,
                        'sort' => $store->sort,
                        'created_at' => $store->created_at,
                        'updated_at' => $store->updated_at,
                    ] : null,
                    'product' => [
                        'id' => $product->id,
                        'store_id' => $product->store_id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'sort' => $product->sort,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                    ],
                ],
            ],
        ];
    }

    /**
     * 批量創建新 Product
     *
     * @param array $productsData
     * @return array
     * @throws \Exception
     */
    public function createBatch(array $productsData): array
    {
        $results = [];
        $maxSortByStore = []; // 記錄每個 store_id 的最大 sort 值

        foreach ($productsData as $data) {
            $storeId = $data['store_id'];

            // 如果該 store_id 還沒有計算過最大值，則計算
            if (!isset($maxSortByStore[$storeId])) {
                $maxSortByStore[$storeId] = $this->productRepository->getMaxSortByStoreId($storeId);
            }

            // 自動設定 sort 為該 store_id 的最大值 + 1
            $maxSortByStore[$storeId]++;
            $data['sort'] = $maxSortByStore[$storeId];

            $product = $this->productRepository->create($data);
            $product->load('store');
            $store = $product->store;

            $results[] = [
                'store' => $store ? [
                    'id' => $store->id,
                    'name' => $store->name,
                    'sort' => $store->sort,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ] : null,
                'product' => [
                    'id' => $product->id,
                    'store_id' => $product->store_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'sort' => $product->sort,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ],
            ];
        }

        return [
            'products' => $results,
        ];
    }

    /**
     * 更新單一 Product
     *
     * @param int $id product ID
     * @param array $data 要更新的資料
     * @return array
     * @throws \Exception
     */
    public function update(int $id, array $data): array
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new \Exception('品項不存在', 404);
        }

        // 如果更新 store_id，驗證新的 store_id 是否存在
        if (isset($data['store_id']) && $data['store_id'] != $product->store_id) {
            $store = \App\Models\Store::find($data['store_id']);
            if (!$store) {
                throw new \Exception('類別不存在', 404);
            }
        }

        // update 方法會自動從 data 中取出 id（但這裡我們已經有 id 了，所以不需要）
        $data['id'] = $id;
        $updated = $this->productRepository->update($data);

        if (!$updated) {
            throw new \Exception('更新品項失敗', 500);
        }

        // 重新取得更新後的資料
        $product->refresh();
        $product->load('store');
        $store = $product->store;

        return [
            'store' => $store ? [
                'id' => $store->id,
                'name' => $store->name,
                'sort' => $store->sort,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
            ] : null,
            'product' => [
                'id' => $product->id,
                'store_id' => $product->store_id,
                'name' => $product->name,
                'price' => $product->price,
                'sort' => $product->sort,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ];
    }

    /**
     * 批量更新 Product（針對特定 store_id）
     *
     * @param int $storeId 要更新的 store_id
     * @param array $productsData products 陣列
     * @return array
     * @throws \Exception
     */
    public function updateBatch(int $storeId, array $productsData): array
    {
        $results = [];
        $maxSort = $this->productRepository->getMaxSortByStoreId($storeId);
        
        // 收集所有傳入的 product ids（只包含有 id 的，用於後續刪除判斷）
        $providedIds = [];
        foreach ($productsData as $item) {
            if (isset($item['id']) && $item['id'] !== null && $item['id'] !== '') {
                $providedIds[] = (int)$item['id'];
            }
        }

        // 處理每個 product
        foreach ($productsData as $item) {
            $hasId = isset($item['id']) && $item['id'] !== null && $item['id'] !== '';

            // 沒有 id -> 新增 product
            if (!$hasId) {
                $maxSort++;
                $item['store_id'] = $storeId; // 使用傳入的 store_id
                $item['sort'] = $maxSort; // 自動設定 sort

                $created = $this->productRepository->create($item);
                $created->load('store');
                $store = $created->store;

                $results[] = [
                    'store' => $store ? [
                        'id' => $store->id,
                        'name' => $store->name,
                        'sort' => $store->sort,
                        'created_at' => $store->created_at,
                        'updated_at' => $store->updated_at,
                    ] : null,
                    'product' => [
                        'id' => $created->id,
                        'store_id' => $created->store_id,
                        'name' => $created->name,
                        'price' => $created->price,
                        'sort' => $created->sort,
                        'created_at' => $created->created_at,
                        'updated_at' => $created->updated_at,
                    ],
                ];

                continue;
            }

            // 有 id -> 更新 product
            $id = (int)$item['id'];
            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception("品項 ID {$id} 不存在", 404);
            }

            // 驗證 product 是否屬於該 store_id
            if ($product->store_id != $storeId) {
                throw new \Exception("品項 ID {$id} 不屬於類別 ID {$storeId}", 422);
            }

            // update 方法會自動從 data 中取出 id
            $updated = $this->productRepository->update($item);

            if (!$updated) {
                throw new \Exception("更新品項 ID {$id} 失敗", 500);
            }

            // 重新取得更新後的資料
            $product->refresh();
            $product->load('store');
            $store = $product->store;

            $results[] = [
                'store' => $store ? [
                    'id' => $store->id,
                    'name' => $store->name,
                    'sort' => $store->sort,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ] : null,
                'product' => [
                    'id' => $product->id,
                    'store_id' => $product->store_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'sort' => $product->sort,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ],
            ];
        }

        // 找出該 store_id 下所有不在傳入陣列中的 products 並軟刪除
        if (!empty($providedIds)) {
            // 取得該 store_id 下所有現有的 product ids
            $allProductsInStore = $this->productRepository->findByStoreId($storeId);
            
            foreach ($allProductsInStore as $product) {
                // 如果 product 的 id 不在傳入的 ids 中，就軟刪除它
                if (!in_array($product->id, $providedIds)) {
                    $this->productRepository->delete($product->id);
                }
            }
        }

        return [
            'products' => $results,
        ];
    }

    /**
     * 刪除 Product（軟刪除）
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new \Exception('品項不存在', 404);
        }

        $deleted = $this->productRepository->delete($id);

        if (!$deleted) {
            throw new \Exception('刪除失敗', 500);
        }
    }
}

