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

    //購入ボタンを押すと購入が完了
    public function test_order() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('item.pay'),[
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment'  => 2,
            'shipping' => 'テスト住所',
        ]);

        $response->assertSessionHasNoErrors();

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

        $this->actingAs($buyer)->post(route('item.pay'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => '東京都渋谷区',
        ]);

        $this->get(route('item.index'))
                ->assertOk()
                ->assertSee($item->name)
                ->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_order_mypage () {

        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer)->post(route('item.pay'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => '東京都渋谷区',
        ]);

        $response = $this->get(route('mypage', ['page' => 'buy']));

        $response->assertOk();
        $response->assertSee($item->name);
    }
}
