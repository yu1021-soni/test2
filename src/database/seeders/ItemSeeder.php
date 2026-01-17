<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $fallbackPath = 'items/noimage.jpg';
        $fallbackFile = database_path('seeders/assets/items/noimage.jpg');

        if (file_exists($fallbackFile)) {
            Storage::disk('public')->put($fallbackPath, file_get_contents($fallbackFile));
        }

        $sellerAId = User::where('email', 'sellerA@example.com')->value('id');
        $sellerBId = User::where('email', 'sellerB@example.com')->value('id');

        // 状態ラベル → コード
        $map = ['良好'=>1,'目立った傷や汚れなし'=>2,'やや傷や汚れあり'=>3,'状態が悪い'=>4];
        $toCode = fn($label) => $map[$label] ?? 0;

        // 画像URL storage保存
        $saveFromUrl = function (string $url): ?string {
            try {
                $res = Http::timeout(20)->get($url);
                if (!$res->successful()) return null;
                $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                $name = 'items/'.Str::uuid().'.'.$ext;
                Storage::disk('public')->put($name, $res->body());
                return $name;
            } catch (\Throwable $e) { return null; }
        };

        $rows = [
            ['A','腕時計','15,000','Rolax','スタイリッシュなデザインのメンズ腕時計','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg','良好',['メンズ','ファッション']],
            ['A','HDD','5,000','西芝','高速で信頼性の高いハードディスク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg','目立った傷や汚れなし','家電'],
            ['A','玉ねぎ3束','300','なし','新鮮な玉ねぎ3束のセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg','やや傷や汚れあり','キッチン'],
            ['A','革靴','4,000','', 'クラシックなデザインの革靴','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg','状態が悪い','メンズ'],
            ['A','ノートPC','45,000','', '高性能なノートパソコン','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg','良好','家電'],

            ['B','マイク','8,000','なし','高音質のレコーディング用マイク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg','目立った傷や汚れなし','家電'],
            ['B','ショルダーバッグ','3,500','', 'おしゃれなショルダーバッグ','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg','やや傷や汚れあり',['レディース','ファッション']],
            ['B','タンブラー','500','なし','使いやすいタンブラー','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg','状態が悪い','キッチン'],
            ['B','コーヒーミル','4,000','Starbacks','手動のコーヒーミル','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg','良好','キッチン'],
            ['B','メイクセット','2,500','', '便利なメイクアップセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg','目立った傷や汚れなし','コスメ'],
        ];

        // カテゴリをまとめて取得
        $allCategories = Category::all();

        // カテゴリ名（文字列 or 配列）→ 既存のID配列
        $resolveCategoryIds = function ($categoryNames) use ($allCategories) {
            if (!is_array($categoryNames)) {
                $categoryNames = [$categoryNames];
            }

            $ids = [];
            foreach ($categoryNames as $name) {
                $category = $allCategories->firstWhere('name', $name);
                if ($category) {
                    $ids[] = $category->id;
                }
            }
            return array_values(array_unique($ids));
        };

        foreach ($rows as [$sellerKey, $name, $price, $brand, $desc, $url, $condLabel, $categoryNames]) {
            $stored = $saveFromUrl($url);

            // 出品者を指定
            $sellerId = ($sellerKey === 'A') ? $sellerAId : $sellerBId;

            $categoryIds = $resolveCategoryIds($categoryNames);

            $item = Item::create([
                'user_id'      => $sellerId,
                'name'         => $name,
                'price'        => (int)str_replace(',', '', $price),
                'brand'        => ($brand === '' || $brand === 'なし') ? null : $brand,
                'description'  => $desc,
                'item_img_url' => $stored ?: $fallbackPath,
                'condition'    => $toCode($condLabel),
            ]);

            $item->categories()->sync($categoryIds);
        }
    }
}
