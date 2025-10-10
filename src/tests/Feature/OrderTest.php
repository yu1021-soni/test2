<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CategorySeeder;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //「購入する」ボタンを押下すると購入が完了する
    public function test_order() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. 商品購入画面を開く
        $this->get(route('purchase.store', ['item_id' => $item->id]))->assertOk();

        //3. 商品を選択して「購入する」ボタンを押下
        $response = $this->post(route('item.pay'),[
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment'  => 2,
            'shipping' => 'テスト住所',
        ]);

        //購入が完了する
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment'  => 2,
            'shipping' => 'テスト住所',
        ]);
    }

    //購入した商品は商品一覧画面にて「sold」と表示される
    public function test_order_user_buy_sold() {

        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        //1. ユーザーにログインする
        $this->actingAs($buyer);

        //2. 商品購入画面を開く
        $response = $this->get(route('purchase.store', ['item_id' => $item->id]));
        $response->assertOk();
        $response->assertSeeText($item->name);

        //3. 商品を選択して「購入する」ボタンを押下
        $response = $this->post(route('item.pay'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => '東京都渋谷区',
        ]);

        $response->assertStatus(302);

        //4. 商品一覧画面を表示する
        $this->get(route('item.index'))
                ->assertOk()
                ->assertSee($item->name)
                //購入した商品が「sold」として表示されている
                ->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_order_mypage () {

        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        //1. ユーザーにログインする
        $this->actingAs($buyer);

        //2. 商品購入画面を開く
        $response = $this->get(route('purchase.store', ['item_id' => $item->id]));
        $response->assertOk();
        $response->assertSeeText($item->name);

        //3. 商品を選択して「購入する」ボタンを押下
        $response = $this->post(route('item.pay'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => '東京都渋谷区',
        ]);
        $response->assertStatus(302);

        //4. プロフィール画面を表示する
        $response = $this->get(route('mypage', ['page' => 'buy']));
        $response->assertOk();
        $response->assertSee($item->name);
    }
}
