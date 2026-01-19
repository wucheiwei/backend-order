# API 文件（backend-order）

本文件集中整理本專案的 API 規格（端點、認證方式、請求/回應範例），避免與 `README.md` 的啟動說明混在一起。

## 統一回應格式（ApiResponse）

所有 API 回應都遵循：

```json
{
  "code": 200,
  "is_success": true,
  "message": "操作成功",
  "data": {}
}
```

### 驗證失敗（422）範例

```json
{
  "code": 422,
  "is_success": false,
  "message": "驗證失敗",
  "data": {
    "errors": {
      "products.0.store_id": ["類別不存在"],
      "products.0.name": ["名稱為必填欄位"]
    }
  }
}
```

## 認證方式（JWT Bearer Token）

所有需要認證的 API 端點，請在 Header 帶入：

```
Authorization: Bearer {your_jwt_token}
Accept: application/json
Content-Type: application/json
```

## 端點列表

### Auth（不需要 Token）

- `POST /api/auth/register`：註冊
- `POST /api/auth/login`：登入

### Auth（需要 Token）

- `GET /api/auth/me`：取得當前會員資訊
- `POST /api/auth/logout`：登出
- `POST /api/auth/refresh`：刷新 Token

### Stores（需要 Token）

#### GET /api/stores - 取得類別列表

**請求方式**: `GET`  
**認證**: 需要 JWT Token  
**說明**: 不分頁，直接回傳所有類別資料，依 `sort` 排序

**請求範例**:
```
GET /api/stores
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "取得類別列表成功",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "飲料類",
        "sort": 1,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      },
      {
        "id": 2,
        "name": "餐點類",
        "sort": 2,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      },
      {
        "id": 3,
        "name": "甜點類",
        "sort": 3,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      }
    ]
  }
}
```

#### GET /api/stores/{id} - 取得單一類別

**請求方式**: `GET`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `id`: 類別 ID

**請求範例**:
```
GET /api/stores/1
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "取得類別成功",
  "data": {
    "id": 1,
    "name": "飲料類",
    "sort": 1,
    "created_at": "2026-01-18T23:36:10.000000Z",
    "updated_at": "2026-01-18T23:36:10.000000Z"
  }
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "類別不存在",
  "data": []
}
```

#### DELETE /api/stores/{id} - 刪除類別（軟刪除）

**請求方式**: `DELETE`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `id`: 類別 ID

**請求範例**:
```
DELETE /api/stores/1
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "刪除類別成功",
  "data": []
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "類別不存在",
  "data": []
}
```

#### POST /api/stores - 批量新增類別

**請求方式**: `POST`  
**認證**: 需要 JWT Token  
**說明**:
- 可以選擇性地在每個 store 中包含 `products` 陣列，一併新增品項
- 如果包含 `products`，會先建立 store（取得 `store_id`），然後再新增 products
- `products` 中的 `sort` **不需傳入**，後端會依「相同 store_id、且排除軟刪除」的最大 sort + 1 自動遞增

**請求範例（不含 products）**:
```json
{
  "stores": [
    { "name": "飲料類" },
    { "name": "餐點類" },
    { "name": "甜點類" }
  ]
}
```

**請求範例（包含 products）**:
```json
{
  "stores": [
    {
      "name": "飲料類",
      "products": [
        { "name": "可樂", "price": 50 },
        { "name": "雪碧", "price": 50 }
      ]
    },
    {
      "name": "餐點類",
      "products": [
        { "name": "漢堡", "price": 100 },
        { "name": "薯條", "price": 40 }
      ]
    },
    {
      "name": "甜點類"
    }
  ]
}
```

**回應範例（不含 products）**:
```json
{
  "code": 201,
  "is_success": true,
  "message": "創建類別成功",
  "data": [
    {
      "id": 1,
      "name": "飲料類",
      "sort": 1,
      "created_at": "2026-01-18T23:36:10.000000Z",
      "updated_at": "2026-01-18T23:36:10.000000Z"
    }
  ]
}
```

**回應範例（包含 products）**:
```json
{
  "code": 201,
  "is_success": true,
  "message": "創建類別成功",
  "data": [
    {
      "id": 1,
      "name": "飲料類",
      "sort": 1,
      "created_at": "2026-01-18T23:36:10.000000Z",
      "updated_at": "2026-01-18T23:36:10.000000Z",
      "products": [
        {
          "store": {
            "id": 1,
            "name": "飲料類",
            "sort": 1,
            "created_at": "2026-01-18T23:36:10.000000Z",
            "updated_at": "2026-01-18T23:36:10.000000Z"
          },
          "product": {
            "id": 1,
            "store_id": 1,
            "name": "可樂",
            "price": 50,
            "sort": 1,
            "created_at": "2026-01-18T23:36:10.000000Z",
            "updated_at": "2026-01-18T23:36:10.000000Z"
          }
        },
        {
          "store": {
            "id": 1,
            "name": "飲料類",
            "sort": 1,
            "created_at": "2026-01-18T23:36:10.000000Z",
            "updated_at": "2026-01-18T23:36:10.000000Z"
          },
          "product": {
            "id": 2,
            "store_id": 1,
            "name": "雪碧",
            "price": 50,
            "sort": 2,
            "created_at": "2026-01-18T23:36:10.000000Z",
            "updated_at": "2026-01-18T23:36:10.000000Z"
          }
        }
      ]
    }
  ]
}
```

#### Stores - 批量更新（PUT /api/stores）

```json
{
  "stores": [
    { "id": 1, "name": "飲料類" },
    { "id": 2, "name": "餐點類" },
    { "id": 3, "name": "甜點類" }
  ]
}
```

### Products（需要 Token）

#### GET /api/products - 取得品項列表

**請求方式**: `GET`  
**認證**: 需要 JWT Token  
**Query 參數**:
- `page` (可選): 頁碼，預設為 1
- `per_page` (可選): 每頁筆數，預設為 10，最多 10 筆

**請求範例**:
```
GET /api/products?page=1&per_page=10
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "取得品項列表成功",
  "data": {
    "products": [
      {
        "store": {
          "id": 1,
          "name": "飲料類",
          "sort": 1,
          "created_at": "2026-01-18T23:36:10.000000Z",
          "updated_at": "2026-01-18T23:36:10.000000Z"
        },
        "product": {
          "id": 1,
          "store_id": 1,
          "name": "可樂",
          "price": 50,
          "sort": 1,
          "created_at": "2026-01-18T23:36:10.000000Z",
          "updated_at": "2026-01-18T23:36:10.000000Z"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 100,
      "last_page": 10
    }
  }
}
```

#### GET /api/products/by-store/{store_id} - 根據 Store ID 取得所有品項

**請求方式**: `GET`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `store_id`: 類別 ID

**請求範例**:
```
GET /api/products/by-store/1
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "取得品項列表成功",
  "data": {
    "products": [
      {
        "id": 1,
        "store_id": 1,
        "name": "可樂",
        "price": 50,
        "sort": 1,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      },
      {
        "id": 2,
        "store_id": 1,
        "name": "雪碧",
        "price": 50,
        "sort": 2,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      }
    ]
  }
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "類別不存在",
  "data": []
}
```

#### GET /api/products/{id} - 取得單一品項

**請求方式**: `GET`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `id`: 品項 ID

**請求範例**:
```
GET /api/products/1
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "取得品項成功",
  "data": {
    "store": {
      "id": 1,
      "name": "飲料類",
      "sort": 1,
      "created_at": "2026-01-18T23:36:10.000000Z",
      "updated_at": "2026-01-18T23:36:10.000000Z"
    },
    "products": [
      {
        "id": 1,
        "store_id": 1,
        "name": "可樂",
        "price": 50,
        "sort": 1,
        "created_at": "2026-01-18T23:36:10.000000Z",
        "updated_at": "2026-01-18T23:36:10.000000Z"
      }
    ]
  }
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "品項不存在",
  "data": []
}
```

#### DELETE /api/products/{id} - 刪除品項（軟刪除）

**請求方式**: `DELETE`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `id`: 品項 ID

**請求範例**:
```
DELETE /api/products/1
Authorization: Bearer {your_jwt_token}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "刪除品項成功",
  "data": []
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "品項不存在",
  "data": []
}
```

#### POST /api/products - 批量新增品項

**說明**:
- `sort` **不需傳入**，後端會依「相同 store_id、且排除軟刪除」的最大 sort + 1 自動遞增
- `store_id` 需存在於 `stores` 且 `deleted_at IS NULL`

#### Products - 批量新增（POST /api/products）

```json
{
  "products": [
    { "store_id": 1, "name": "可樂", "price": 50 },
    { "store_id": 1, "name": "雪碧", "price": 50 },
    { "store_id": 2, "name": "漢堡", "price": 100 }
  ]
}
```

#### PUT /api/products/{id} - 更新單一品項

**請求方式**: `PUT`  
**認證**: 需要 JWT Token  
**路徑參數**:
- `id`: 品項 ID

**請求範例**:
```
PUT /api/products/1
Authorization: Bearer {your_jwt_token}
Content-Type: application/json

{
  "name": "可樂（大）",
  "price": 60,
  "store_id": 1,
  "sort": 1
}
```

**說明**:
- 所有欄位都是可選的（`sometimes`），只需要傳入要更新的欄位
- `store_id` 可以更新（會驗證新的 store_id 是否存在且未軟刪除）
- `sort` 可以更新

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "更新品項成功",
  "data": {
    "store": {
      "id": 1,
      "name": "飲料類",
      "sort": 1,
      "created_at": "2026-01-18T23:36:10.000000Z",
      "updated_at": "2026-01-18T23:36:10.000000Z"
    },
    "product": {
      "id": 1,
      "store_id": 1,
      "name": "可樂（大）",
      "price": 60,
      "sort": 1,
      "created_at": "2026-01-18T23:36:10.000000Z",
      "updated_at": "2026-01-18T23:36:10.000000Z"
    }
  }
}
```

**錯誤回應（404）**:
```json
{
  "code": 404,
  "is_success": false,
  "message": "品項不存在",
  "data": []
}
```

#### PUT /api/products/batch - 批量更新品項

**請求方式**: `PUT`  
**認證**: 需要 JWT Token  

**說明（重要）**：
- **必須傳入 `store_id`**：指定要更新的類別 ID
- 只能更新**單一類別**下的 products，不能同時更新多個類別
- 若單筆資料 **有 `id`**：視為更新該 product（必須屬於指定的 `store_id`）
- 若單筆資料 **沒有 `id`**：視為新增 product（`name` / `price` 為必填，`store_id` 會自動使用外層的）
  - `sort` **不需傳入**，後端會依「相同 store_id、且排除軟刪除」的最大 sort + 1 自動遞增
- **同步刪除規則**：更新完成後，該 `store_id` 下所有不在傳入陣列中的 products 會被軟刪除
  - 也就是說：本次 `PUT /api/products` 的 `products`（含更新 + 新增）會被視為該類別的「最新完整清單」
  - 若本次傳入全部都沒有 `id`（全新增），後端**不會**觸發同步刪除（避免把既有資料全刪掉）

**請求格式**:
```json
{
  "store_id": 1,
  "products": [
    { "id": 1, "name": "可樂（大）", "price": 60 },
    { "id": 2, "name": "雪碧（大）", "price": 60 },
    { "name": "新飲料", "price": 50 }
  ]
}
```

**回應範例**:
```json
{
  "code": 200,
  "is_success": true,
  "message": "更新品項成功",
  "data": {
    "products": [
      {
        "store": {
          "id": 1,
          "name": "飲料類",
          "sort": 1,
          "created_at": "2026-01-18T23:36:10.000000Z",
          "updated_at": "2026-01-18T23:36:10.000000Z"
        },
        "product": {
          "id": 1,
          "store_id": 1,
          "name": "可樂（大）",
          "price": 60,
          "sort": 1,
          "created_at": "2026-01-18T23:36:10.000000Z",
          "updated_at": "2026-01-18T23:36:10.000000Z"
        }
      }
    ]
  }
}
```

## Products 回傳格式（重點）

`data.products` 是一個陣列，**每一筆都包含 product 與關聯 store**：

```json
{
  "code": 200,
  "is_success": true,
  "message": "取得品項列表成功",
  "data": {
    "products": [
      {
        "store": { "id": 1, "name": "飲料類", "sort": 1, "created_at": "...", "updated_at": "..." },
        "product": { "id": 10, "store_id": 1, "name": "可樂", "price": 50, "sort": 6, "created_at": "...", "updated_at": "..." }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 100,
      "last_page": 10
    }
  }
}
```


