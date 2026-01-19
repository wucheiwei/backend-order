<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * 取得所有 Product（分頁，包含關聯的 Store）
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = min((int)$request->input('per_page', 10), 10); // 最多 10 筆
            $data = $this->productService->getAll($perPage);

            return $this->success($data, '取得品項列表成功');
        } catch (\Exception $e) {
            return $this->serverError('取得品項列表失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得單一 Product（包含關聯的 Store）
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $data = $this->productService->getById($id);

            return $this->success($data, '取得品項成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('取得品項失敗：' . $e->getMessage());
        }
    }

    /**
     * 批量創建新 Product
     *
     * @param CreateProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->productService->createBatch($validated['products']);

            return $this->success($data, '創建品項成功', 201);
        } catch (\Exception $e) {
            return $this->serverError('創建品項失敗：' . $e->getMessage());
        }
    }

    /**
     * 批量更新 Product
     *
     * @param UpdateProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->productService->updateBatch($validated['products']);

            return $this->success($data, '更新品項成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('更新品項失敗：' . $e->getMessage());
        }
    }

    /**
     * 刪除 Product（軟刪除）
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->productService->delete($id);

            return $this->success([], '刪除品項成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 404) {
                return $this->notFound($e->getMessage());
            }
            return $this->serverError('刪除品項失敗：' . $e->getMessage());
        }
    }
}

