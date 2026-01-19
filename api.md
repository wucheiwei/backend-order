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
**Query 參數**:
- `page` (可選): 頁碼，預設為 1
- `per_page` (可選): 每頁筆數，預設為 10，最多 10 筆

**請求範例**:
```
GET /api/stores?page=1&per_page=10
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
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 3,
      "last_page": 1
    }
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

#### Stores - 批量新增（POST /api/stores）

```json
{
  "stores": [
    { "name": "飲料類" },
    { "name": "餐點類" },
    { "name": "甜點類" }
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

#### Products - 批量更新（PUT /api/products）

```json
{
  "products": [
    { "id": 1, "name": "可樂（大）", "price": 60 },
    { "id": 2, "store_id": 2, "name": "薯條", "price": 40 }
  ]
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


