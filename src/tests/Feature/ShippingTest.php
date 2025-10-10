<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;

class ShippingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_shipping_change_register() {

        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id]);

        //1. ユーザーにログインする
        $this->actingAs($buyer);

        //2. 送付先住所変更画面で住所を登録する
        $this->withSession([
            "checkout.address.{$item->id}" => [
                'postcode' => 'テストpostcode',
                'address'  => 'テスト住所',
                'building' => 'テストビル',
            ],
        ]);

        //3. 商品購入画面を再度開く
        $this->get(route('purchase.store', ['item_id' => $item->id]))
            ->assertOk()
            //登録した住所が商品購入画面に正しく反映される
            ->assertSee('テストpostcode')
            ->assertSee('テスト住所')
            ->assertSee('テストビル');
    }

    //購入した商品に送付先住所が紐づいて登録される
    public function test_shipping_link_item() {

        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        //1. ユーザーにログインする
        $this->actingAs($buyer);

        //2. 送付先住所変更画面で住所を登録する
        $this->post(route('address.change'), [
            'postcode' => 'テストpostcode',
            'address'  => 'テスト住所',
            'building' => 'テストビル',
        ]);

        //3. 商品を購入する
        $this->post(route('item.pay'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => 'テストpostcode テスト住所 テストビル',
        ]);

        //正しく送付先住所が紐づいている
        $this->assertDatabaseHas('orders', [
            'user_id'  => $buyer->id,
            'item_id'  => $item->id,
            'shipping' => 'テストpostcode テスト住所 テストビル',
        ]);
    }
}
