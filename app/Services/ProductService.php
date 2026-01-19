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
     * 批量更新 Product
     *
     * @param array $productsData
     * @return array
     * @throws \Exception
     */
    public function updateBatch(array $productsData): array
    {
        $results = [];
        $maxSortByStore = [];

        // 先依 store_id 分組（沒有傳 store_id 的更新資料，會在處理時用原本 product.store_id）
        $groups = [];
        foreach ($productsData as $item) {
            $groupKey = $item['store_id'] ?? '__unknown_store__';
            $groups[$groupKey][] = $item;
        }

        foreach ($groups as $groupStoreId => $items) {
            foreach ($items as $item) {
                $hasId = isset($item['id']) && $item['id'] !== null && $item['id'] !== '';

                // 沒有 id -> 新增 product
                if (!$hasId) {
                    $storeId = (int)($item['store_id'] ?? 0);
                    if ($storeId <= 0) {
                        // 理論上會被 FormRequest 擋下；這裡保險
                        throw new \Exception("products 中存在未帶 store_id 的新增資料", 422);
                    }

                    if (!isset($maxSortByStore[$storeId])) {
                        $maxSortByStore[$storeId] = $this->productRepository->getMaxSortByStoreId($storeId);
                    }
                    $maxSortByStore[$storeId]++;
                    $item['sort'] = $maxSortByStore[$storeId];

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

