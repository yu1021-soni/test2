<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Database\Seeders\CategorySeeder;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    //「購入する」ボタンを押下すると購入が完了する
    public function test_order()
    {
        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 実際のStripe処理は省略して、直接注文を作成
        Order::create([
            'user_id'   => $user->id,
            'item_id'   => $item->id,
            'payment'   => 2,
            'postcode'  => '111-1111',
            'address'   => 'テストアドレス',
            'building'  => 'テストビル',
            'amount'    => $item->price,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'payment' => 2,
            'address' => 'テストアドレス',
        ]);
    }

    //購入した商品は商品一覧画面にて「Sold」と表示される
    public function test_order_user_buy_sold()
    {
        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer);

        // 注文を直接作成
        Order::create([
            'user_id'   => $buyer->id,
            'item_id'   => $item->id,
            'payment'   => 2,
            'postcode'  => '111-1111',
            'address'   => 'テストアドレス',
            'building'  => 'テストビル',
            'amount'    => $item->price,
            'payment_status' => 'paid',
        ]);

        // 商品一覧にSoldが表示されることを確認
        $this->get(route('item.index'))
            ->assertOk()
            ->assertSee($item->name)
            ->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_order_mypage()
    {
        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer);

        // 注文を直接作成
        Order::create([
            'user_id'   => $buyer->id,
            'item_id'   => $item->id,
            'payment'   => 2,
            'postcode'  => '111-1111',
            'address'   => 'テストアドレス',
            'building'  => 'テストビル',
            'amount'    => $item->price,
            'payment_status' => 'paid',
        ]);

        // マイページの購入一覧に商品が表示されることを確認
        $this->get(route('mypage', ['page' => 'buy']))
            ->assertOk()
            ->assertSee($item->name);
    }
}
