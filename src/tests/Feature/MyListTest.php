<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CategorySeeder;

class MyListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_mylist () {

        //Categoryデータが入ってないとItem::factory()->create()が動かない
        $this->seed(CategorySeeder::class);

        //テスト用ユーザ
        $user = User::factory()->create();
        //テスト用の商品作成
        //factory 最低限必要な部分だけ指定して、残りは「ランダム値」で埋めてくれる
        $myitem = Item::factory()->create(['name' => 'テスト用商品']);

        //favoriteテーブルに一件追加
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $myitem->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //ログイン アクセス
        $this->actingAs($user);
        $response = $this->get('/?tab=mylist');

        $response->assertOk();
        $response->assertSeeText('テスト用商品');
    }

    public function test_mylist_user_buy_sold() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '購入済み商品']);

         // いいね登録
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 購入済みにする
        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        //ログイン アクセス
        $this->actingAs($user);
        $response = $this->get('/?tab=mylist');

        $response->assertOk();
        $response->assertSeeText('購入済み商品');
        $response->assertSeeText('Sold');
    }

    //未承認の場合はmylist表示しない
    public function test_mylist_not_login() {

        $this->seed(CategorySeeder::class);

        Item::factory()->create(['name' => 'myitem']);

        $response = $this->get('/?tab=mylist');
        $response->assertOk();
        $response->assertDontSeeText('myitem');
    }
}
