# API 回應規格書

## 統一回應格式

所有 API 回應都必須遵循以下 JSON 格式：

```json
{
  "code": 200,
  "is_success": true,
  "message": "操作成功",
  "data": []
}
```

## 欄位說明

### code (integer, required)
HTTP 狀態碼或自定義錯誤碼
- `200`: 成功
- `400`: 請求參數錯誤
- `401`: 未授權
- `403`: 禁止訪問
- `404`: 資源不存在
- `422`: 驗證失敗
- `500`: 伺服器錯誤

### is_success (boolean, required)
操作是否成功
- `true`: 操作成功
- `false`: 操作失敗

### message (string, required)
回應訊息，用於描述操作結果或錯誤原因

### data (array|object|null, required)
回應資料
- 成功時：包含實際資料（可以是陣列或物件）
- 失敗時：可以是空陣列 `[]`、空物件 `{}` 或 `null`

## 範例

### 成功回應
```json
{
  "code": 200,
  "is_success": true,
  "message": "操作成功",
  "data": {
    "id": 1,
    "name": "範例資料"
  }
}
```

### 成功回應（列表）
```json
{
  "code": 200,
  "is_success": true,
  "message": "查詢成功",
  "data": [
    {"id": 1, "name": "項目1"},
    {"id": 2, "name": "項目2"}
  ]
}
```

### 錯誤回應（404）
```json
{
  "code": 404,
  "is_success": false,
  "message": "資源不存在",
  "data": []
}
```

### 錯誤回應（400）
```json
{
  "code": 400,
  "is_success": false,
  "message": "請求參數錯誤：缺少必要欄位",
  "data": []
}
```

### 錯誤回應（422 驗證失敗）
```json
{
  "code": 422,
  "is_success": false,
  "message": "驗證失敗",
  "data": {
    "errors": {
      "email": ["電子郵件格式不正確"],
      "password": ["密碼長度至少需要 8 個字元"]
    }
  }
}
```

## 實作規範

1. 所有 API Controller 必須使用統一的回應格式
2. 使用 `ApiResponse` Trait 或 Helper 函數來產生回應
3. 錯誤訊息應該清晰明確，便於前端處理和顯示
4. 成功時 `code` 應為 `200`，`is_success` 為 `true`
5. 失敗時 `code` 應為對應的 HTTP 狀態碼，`is_success` 為 `false`

