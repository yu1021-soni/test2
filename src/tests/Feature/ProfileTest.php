<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Database\Seeders\CategorySeeder;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //必要な情報が取得できる
    public function test_profile_get() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create([
            'name'        => 'テスト名前',
            'email' => 'taro@example.com',
            'user_img_url' => 'avatars/test.png',
        ]);

        //1. ユーザーにログインする
        $this->actingAs($user);

        // 出品商品
        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name'    => 'テスト出品商品',
        ]);

        // 購入商品
        $buyItem = Item::factory()->create(['name' => 'テスト購入商品']);

        Order::create([
            'user_id'  => $user->id,
            'item_id'  => $buyItem->id,
            'payment'  => 2,
            'postcode' => '111-1111',
            'address'  => 'テストアドレス',
            'building' => 'テストビル',
        ]);

        //2.プロフィールページを開く
        $this->get(route('mypage'))
            ->assertOk()
            ->assertSee('テスト名前')
            ->assertSee('/storage/avatars/test.png');

        // 出品一覧タブ
        $this->get(route('mypage', ['page' => 'sell']))
            ->assertOk()
            ->assertSee($sellItem->name);

        // 購入一覧タブ
        $this->get(route('mypage', ['page' => 'buy']))
            ->assertOk()
            ->assertSee('テスト購入商品');
    }
}
