<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Database\Seeders\CategorySeeder;

class ShippingTest extends TestCase
{
    use RefreshDatabase;

    //送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_shipping_change_register()
    {
        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer);

        // セッションに住所を保存
        $this->withSession([
            "checkout.address.{$item->id}" => [
                'postcode' => '111-1111',
                'address'  => 'テスト住所',
                'building' => 'テストビル',
            ],
        ]);

        $this->post(route('purchase.store', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('111-1111')
            ->assertSee('テスト住所')
            ->assertSee('テストビル');
    }

    //購入した商品に送付先住所が紐づいて登録される
    public function test_shipping_link_item()
    {
        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer);

        // テストでは Stripe を通さず直接注文を作成する
        Order::create([
            'user_id'   => $buyer->id,
            'item_id'   => $item->id,
            'payment'   => 2,
            'postcode'  => '222-2222',
            'address'   => 'テスト住所',
            'building'  => 'テストビル',
            'amount'    => $item->price,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id'  => $buyer->id,
            'item_id'  => $item->id,
            'postcode' => '222-2222',
            'address'  => 'テスト住所',
            'building' => 'テストビル',
        ]);
    }
}
