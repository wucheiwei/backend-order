<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清除現有資料（可選，用於重新產生測試資料）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Store::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 建立測試會員
        $user = User::create([
            'name' => '測試會員',
            'email' => 'test@123.tw',
            'password' => base64_encode('12345678'), // base64 編碼密碼
        ]);

        $this->command->info('已建立測試會員：test@123.tw / 12345678');

        // 建立測試類別（Stores）
        $stores = [
            ['name' => '飲料類', 'sort' => 1],
            ['name' => '餐點類', 'sort' => 2],
            ['name' => '甜點類', 'sort' => 3],
            ['name' => '小食類', 'sort' => 4],
        ];

        $createdStores = [];
        foreach ($stores as $storeData) {
            $store = Store::create($storeData);
            $createdStores[] = $store;
            $this->command->info("已建立類別：{$store->name}");
        }

        // 建立測試品項（Products）
        $products = [
            // 飲料類
            ['store_id' => $createdStores[0]->id, 'name' => '可樂', 'price' => 50, 'sort' => 1],
            ['store_id' => $createdStores[0]->id, 'name' => '雪碧', 'price' => 50, 'sort' => 2],
            ['store_id' => $createdStores[0]->id, 'name' => '紅茶', 'price' => 40, 'sort' => 3],
            ['store_id' => $createdStores[0]->id, 'name' => '綠茶', 'price' => 40, 'sort' => 4],
            ['store_id' => $createdStores[0]->id, 'name' => '奶茶', 'price' => 60, 'sort' => 5],
            
            // 餐點類
            ['store_id' => $createdStores[1]->id, 'name' => '漢堡', 'price' => 100, 'sort' => 1],
            ['store_id' => $createdStores[1]->id, 'name' => '薯條', 'price' => 40, 'sort' => 2],
            ['store_id' => $createdStores[1]->id, 'name' => '雞塊', 'price' => 80, 'sort' => 3],
            ['store_id' => $createdStores[1]->id, 'name' => '義大利麵', 'price' => 150, 'sort' => 4],
            
            // 甜點類
            ['store_id' => $createdStores[2]->id, 'name' => '蛋糕', 'price' => 120, 'sort' => 1],
            ['store_id' => $createdStores[2]->id, 'name' => '布丁', 'price' => 50, 'sort' => 2],
            ['store_id' => $createdStores[2]->id, 'name' => '冰淇淋', 'price' => 70, 'sort' => 3],
            
            // 小食類
            ['store_id' => $createdStores[3]->id, 'name' => '洋芋片', 'price' => 45, 'sort' => 1],
            ['store_id' => $createdStores[3]->id, 'name' => '餅乾', 'price' => 35, 'sort' => 2],
            ['store_id' => $createdStores[3]->id, 'name' => '巧克力', 'price' => 60, 'sort' => 3],
        ];

        // 建立 store_id 到 store 的映射，方便後續查找
        $storeMap = [];
        foreach ($createdStores as $store) {
            $storeMap[$store->id] = $store;
        }

        foreach ($products as $productData) {
            $product = Product::create($productData);
            $storeName = $storeMap[$productData['store_id']]->name;
            $this->command->info("已建立品項：{$product->name}（{$storeName}）");
        }

        $this->command->info('');
        $this->command->info('✅ 測試資料建立完成！');
        $this->command->info('📊 統計：');
        $this->command->info('   - 會員：1 筆');
        $this->command->info('   - 類別：' . count($createdStores) . ' 筆');
        $this->command->info('   - 品項：' . count($products) . ' 筆');
    }
}

