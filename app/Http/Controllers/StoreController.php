<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Services\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use ApiResponse;

    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * 取得所有 Store（不分頁）
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $stores = $this->storeService->getAll();

            return $this->success([
                'data' => $stores,
            ], '取得類別列表成功');
        } catch (\Exception $e) {
            return $this->serverError('取得類別列表失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得單一 Store
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $store = $this->storeService->getById($id);

            return $this->success($store, '取得類別成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('取得類別失敗：' . $e->getMessage());
        }
    }

    /**
     * 批量創建新 Store
     *
     * @param CreateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $stores = $this->storeService->createBatch($validated['stores']);

            return $this->success($stores, '創建類別成功', 201);
        } catch (\Exception $e) {
            return $this->serverError('創建類別失敗：' . $e->getMessage());
        }
    }

    /**
     * 批量更新 Store
     *
     * @param UpdateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $stores = $this->storeService->updateBatch($validated['stores']);

            return $this->success($stores, '更新類別成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('更新類別失敗：' . $e->getMessage());
        }
    }

    /**
     * 刪除 Store（軟刪除）
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->storeService->delete($id);

            return $this->success([], '刪除類別成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('刪除類別失敗：' . $e->getMessage());
        }
    }
}

