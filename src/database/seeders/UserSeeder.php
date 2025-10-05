<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        User::insert([
            [
                'name' => '佐藤 太郎',
                'email' => 'sato.taro@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '100-0001',
                'address'  => '東京都千代田区千代田',
                'building' => '千代田ビル10F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '鈴木 花子',
                'email' => 'suzuki.hanako@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '150-0001',
                'address'  => '東京都渋谷区神宮前',
                'building' => '青山ビル3F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '高橋 健',
                'email' => 'takahashi.ken@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '060-0001',
                'address'  => '北海道札幌市中央区大通西',
                'building' => '大通タワー12F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '田中 美咲',
                'email' => 'tanaka.misaki@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '530-0001',
                'address'  => '大阪府大阪市北区梅田',
                'building' => '梅田スクエア5F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '伊藤 翔',
                'email' => 'ito.sho@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '460-0002',
                'address'  => '愛知県名古屋市中区丸の内',
                'building' => '丸の内センタービル7F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '中村 彩',
                'email' => 'nakamura.aya@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '810-0001',
                'address'  => '福岡県福岡市中央区天神',
                'building' => '天神プラザ2F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '小林 大輔',
                'email' => 'kobayashi.daisuke@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'postcode' => '980-0011',
                'address'  => '宮城県仙台市青葉区上杉',
                'building' => '上杉ビル6F',
                'user_img_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
