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

    //商品一覧取得
    public function test_item_list() {

        //固定データはFactory不要
        $this->seed(CategorySeeder::class);

        $items = Item::factory()->create();

        $response = $this->get(route('item.index'));

        //ページが存在するか確認
        $response->assertOk();

        //商品の名前が画面に出ているか確認
        $response->assertSee($items->name);
    }

    //購入済み商品sold表示
    public function test_item_sold() {

        $this->seed(CategorySeeder::class);

        $purchaser = User::factory()->create();
        $item = Item::factory()->create();

        //動的データはFactory必要
        Order::factory()->create([
            'user_id' => $purchaser->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.index'));

        //ページが存在するか確認
        $response->assertOk();
        //商品の名前が画面に出ているか確認
        $response->assertSee($item->name);
        //sold表示
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

        //ログイン状態
        $this->actingAs($user);

        $response = $this->get(route('item.index'));

        $response->assertDontSee($myItem->name);
    }
}
