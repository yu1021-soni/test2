<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;               // ← 追加
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者
        $sellerId = User::query()->value('id') ?? User::factory()->create([
            'name' => 'Seeder Seller', 'email' => 'seller@example.com',
        ])->id;

        // ★ 未分類カテゴリを用意して $categoryId を取得（fillable未設定でも動くように手動save）
        $cat = Category::where('name', '未分類')->first();
        if (!$cat) {
            $cat = new Category();
            $cat->name = '未分類';
            $cat->save();
        }
        $categoryId = $cat->id; // ← これを後続で使う

        // 状態ラベル → コード
        $map = ['良好'=>1,'目立った傷や汚れなし'=>2,'やや傷や汚れあり'=>3,'状態が悪い'=>4];
        $toCode = fn($label) => $map[$label] ?? 0;

        // 画像URL→storage保存
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
            ['腕時計','15,000','Rolax','スタイリッシュなデザインのメンズ腕時計','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg','良好'],
            ['HDD','5,000','西芝','高速で信頼性の高いハードディスク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg','目立った傷や汚れなし'],
            ['玉ねぎ3束','300','なし','新鮮な玉ねぎ3束のセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg','やや傷や汚れあり'],
            ['革靴','4,000','', 'クラシックなデザインの革靴','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg','状態が悪い'],
            ['ノートPC','45,000','', '高性能なノートパソコン','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg','良好'],
            ['マイク','8,000','なし','高音質のレコーディング用マイク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg','目立った傷や汚れなし'],
            ['ショルダーバッグ','3,500','', 'おしゃれなショルダーバッグ','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg','やや傷や汚れあり'],
            ['タンブラー','500','なし','使いやすいタンブラー','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg','状態が悪い'],
            ['コーヒーミル','4,000','Starbacks','手動のコーヒーミル','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg','良好'],
            ['メイクセット','2,500','', '便利なメイクアップセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg','目立った傷や汚れなし'],
        ];

        foreach ($rows as [$name, $price, $brand, $desc, $url, $condLabel]) {
            $stored = $saveFromUrl($url);

            Item::create([
                'user_id'       => $sellerId,
                'category_id'   => $categoryId,     // ★ 必須FKを投入
                'name'          => $name,
                'price'         => (int)str_replace(',', '', $price),
                'brand'         => ($brand === '' || $brand === 'なし') ? null : $brand,
                'description'   => $desc,
                'item_img_url'  => $stored ?: $url,  // storage相対 or 外部URL
                'condition'     => $toCode($condLabel),
            ]);
        }
    }
}
