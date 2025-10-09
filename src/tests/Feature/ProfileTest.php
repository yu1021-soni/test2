<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_profile_get() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create([
            'name'        => 'テスト名前',
            'user_img_url'=> 'avatars/test.png',
        ]);

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
            'shipping' => 'テスト住所',
        ]);

        // 出品一覧タブ
        $this->get(route('mypage', ['page' => 'sell']))
            ->assertOk()
            ->assertSee('テスト出品商品');

        // 購入一覧タブ
        $this->get(route('mypage', ['page' => 'buy']))
            ->assertOk()
            ->assertSee('テスト購入商品');

    // 共通要素（ユーザー名・プロフィール画像）
    $this->get(route('mypage'))
        ->assertSee('テスト名前')
        ->assertSee('avatars/test.png');
    }
}
