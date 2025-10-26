<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;

class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //小計画面で変更が反映される
    public function test_payment() {
        $this->seed(CategorySeeder::class);

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        // 商品を出品
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer);

        //1. 支払い方法選択画面を開く
        $response = $this->post(route('purchase.store', ['item_id' => $item->id]));
        $response->assertOk()
                    ->assertSee('支払い方法')
                    ->assertSee('コンビニ払い')
                    ->assertSee('カード払い');

        //2. プルダウンメニューから支払い方法を選択する
        $response = $this->post(route('purchase.store'), [
            'item_id'  => $item->id,
            'payment'  => 2,
            'shipping' => 'テスト住所',
        ]);
        
        //選択した支払い方法が正しく反映される
        $response = $this->post(route('purchase.store', ['item_id' => $item->id]));
        $response->assertOk()
                ->assertSee('カード払い');
    }
}
