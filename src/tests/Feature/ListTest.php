<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Database\Seeders\CategorySeeder;

class ListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //全商品を取得できる
    public function test_item_list() {

        //固定データはFactory不要
        $this->seed(CategorySeeder::class);

        $items = Item::factory()->create();

        //1. 商品ページを開く
        $response = $this->get(route('item.index'));

        //ページが存在するか確認
        $response->assertOk();

        //すべての商品が表示される
        $response->assertSee($items->name);
    }

    //購入済み商品は「Sold」と表示される
    public function test_item_sold() {

        $this->seed(CategorySeeder::class);

        $purchaser = User::factory()->create();
        $item = Item::factory()->create();

        //動的データはFactory必要
        Order::factory()->create([
            'user_id' => $purchaser->id,
            'item_id' => $item->id,
        ]);

        //1. 商品ページを開く
        $response = $this->get(route('item.index'));

        //ページが存在するか確認
        $response->assertOk();

        //2. 購入済み商品を表示する
        $response->assertSee($item->name);

        //購入済み商品に「Sold」のラベルが表示される
        $response->assertSee('Sold');
    }

    //自分が出品した商品は表示されない
    public function test_user_does_not_see_their_own_items() {

        $this->seed(CategorySeeder::class);

        $user  = User::factory()->create();
        $myItem = Item::factory()->create([
            'user_id' => $user->id, // 自分が出品した商品
            'name'    => 'my_listing',
        ]);

        //1. ユーザーにログインをする
        $this->actingAs($user);

        //2. 商品ページを開く
        $response = $this->get(route('item.index'));

        //自分が出品した商品が一覧に表示されない
        $response->assertDontSee($myItem->name);
    }
}
