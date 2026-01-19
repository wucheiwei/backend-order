# Backend Order API

Laravel 10 後端訂單管理系統 API，支援會員認證、類別管理、品項管理等功能。

## 技術棧

- **PHP**: 8.2
- **Laravel**: 10.10
- **MySQL**: 8.0
- **Nginx**: Alpine
- **JWT**: tymon/jwt-auth 2.2

## 功能特色

- ✅ JWT Token 認證系統
- ✅ 會員註冊、登入、登出
- ✅ 登入記錄追蹤
- ✅ 類別（Store）CRUD（支援批量操作）
- ✅ 品項（Product）CRUD（支援批量操作）
- ✅ 軟刪除功能
- ✅ 統一的 API 回應格式
- ✅ 分頁功能

## 專案結構

```
backend-order/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # 控制器
│   │   ├── Middleware/      # 中介層
│   │   ├── Requests/        # 請求驗證
│   │   └── Traits/          # ApiResponse Trait
│   ├── Models/              # Eloquent 模型
│   ├── Repositories/        # 資料庫操作層
│   └── Services/            # 業務邏輯層
├── database/
│   └── migrations/          # 資料庫遷移檔案
├── routes/
│   └── api.php             # API 路由
├── docker-compose.yml      # Docker Compose 配置
└── Dockerfile              # PHP-FPM 容器配置
```

## 安裝與啟動

### 方式一：使用 Docker（推薦）

#### 前置需求
- Docker
- Docker Compose

#### 啟動步驟

1. **複製環境變數檔案**
   ```bash
   cp .env.example .env
   ```

2. **設定環境變數**
   
   編輯 `.env` 檔案，設定以下資料庫連線資訊：
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=backend-order
   DB_USERNAME=root
   DB_PASSWORD=root
   ```

3. **啟動 Docker 容器**
   ```bash
   docker-compose up -d
   ```

4. **進入應用程式容器**
   ```bash
   docker-compose exec app bash
   ```

5. **安裝依賴套件**
   ```bash
   composer install
   ```

6. **產生應用程式金鑰**
   ```bash
   php artisan key:generate
   ```

7. **產生 JWT Secret**
   ```bash
   php artisan jwt:secret
   ```

8. **執行資料庫遷移**
   ```bash
   php artisan migrate
   ```

9. **設定儲存目錄權限**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

#### 服務資訊

- **API 端點**: http://localhost:8000
- **MySQL**: localhost:3308
  - 資料庫名稱: `backend-order`
  - 使用者名稱: `root`
  - 密碼: `root`

#### 常用 Docker 指令

```bash
# 啟動所有服務
docker-compose up -d

# 停止所有服務
docker-compose down

# 查看服務狀態
docker-compose ps

# 查看日誌
docker-compose logs -f

# 重新建立容器
docker-compose up -d --build

# 進入應用程式容器
docker-compose exec app bash

# 進入 MySQL 容器
docker-compose exec mysql bash

# 執行 Artisan 指令
docker-compose exec app php artisan [command]
```

---

### 方式二：不使用 Docker（本地環境）

#### 前置需求
- PHP >= 8.1
- Composer
- MySQL >= 8.0
- Nginx 或 Apache

#### 啟動步驟

1. **複製環境變數檔案**
   ```bash
   cp .env.example .env
   ```

2. **安裝依賴套件**
   ```bash
   composer install
   ```

3. **設定環境變數**
   
   編輯 `.env` 檔案，設定資料庫連線資訊：
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=backend-order
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **產生應用程式金鑰**
   ```bash
   php artisan key:generate
   ```

5. **產生 JWT Secret**
   ```bash
   php artisan jwt:secret
   ```

6. **建立資料庫**
   ```sql
   CREATE DATABASE `backend-order` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

7. **執行資料庫遷移**
   ```bash
   php artisan migrate
   ```

8. **設定儲存目錄權限**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

9. **啟動開發伺服器**
   ```bash
   php artisan serve
   ```
   
   伺服器將在 http://localhost:8000 啟動

#### Nginx 配置範例

```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/backend-order/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## API 文件

API 端點、認證方式、請求/回應範例請見：`api.md`

## 資料庫結構

### 主要資料表

- `users` - 會員資料
- `member_login_logs` - 會員登入記錄
- `stores` - 類別資料
- `products` - 品項資料

## 開發指令

### 在 Docker 容器中執行

```bash
# 進入容器
docker-compose exec app bash

# 執行 Artisan 指令
docker-compose exec app php artisan [command]

# 執行 Migration
docker-compose exec app php artisan migrate

# 執行 Migration Rollback
docker-compose exec app php artisan migrate:rollback

# 查看路由列表
docker-compose exec app php artisan route:list

# 清除快取
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### 在本地環境執行

```bash
# 執行 Artisan 指令
php artisan [command]

# 執行 Migration
php artisan migrate

# 查看路由列表
php artisan route:list

# 清除快取
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 注意事項

1. **JWT Token**: Token 預設有效期限為 60 分鐘，可在 `config/jwt.php` 中調整
2. **軟刪除**: Store 和 Product 都支援軟刪除，刪除的資料不會真正從資料庫移除
3. **批量操作**: Store 和 Product 的 create 和 update 都支援批量操作
4. **分頁**: 所有列表 API 都支援分頁，預設每頁 10 筆，最多 10 筆
5. **排序**: 
   - Store 的 `sort` 欄位在創建時自動設定，更新時不可修改
   - Product 的 `sort` 欄位在創建時自動設定（按相同 store_id 的最大值 + 1），更新時可修改

## 問題排除

### Docker 相關

- **容器無法啟動**: 檢查端口是否被占用（8000, 3308）
- **資料庫連線失敗**: 確認 MySQL 容器已完全啟動（等待約 30 秒）
- **權限問題**: 執行 `chmod -R 775 storage bootstrap/cache`

### 本地環境相關

- **Composer 安裝失敗**: 確認 PHP 版本 >= 8.1
- **資料庫連線失敗**: 檢查 `.env` 中的資料庫設定
- **JWT Secret 未設定**: 執行 `php artisan jwt:secret`

## 授權

MIT License
