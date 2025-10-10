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

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //いいねアイコンを押下することによって、いいねした商品として登録することができる
    public function test_favorite_register () {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. 商品詳細ページを開く
        $this->get(route('items.detail', ['item_id' => $item->id]))->assertOk();

        //お気に入り登録の時よく使う
        //3. いいねアイコンを押下
        $response = $this->post(route('favorites.favorite'), ['item_id' => $item->id]);
        $response->assertStatus(302);

        //いいねした商品として登録され、いいね合計値が増加表示される
        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertSeeInOrder([
                '<i class="fa-solid fa-star"></i>', // アイコン
                '<p>1</p>'                          // いいね数
        ], false);
    }

    //再度いいねアイコンを押下することによって、いいねを解除することができる
    public function test_favorite_not_register () {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. 商品詳細ページを開く
        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('<i class="fa-regular fa-star"></i>', false);

        //お気に入り登録の時よく使う
        //3. いいねアイコンを押下
        $response = $this->post(route('favorites.favorite'), ['item_id' => $item->id]);
        $response->assertStatus(302);

        // DBに登録されたか
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSeeInOrder([
                '<i class="fa-solid fa-star"></i>',
                '<p>1</p>',
            ], false)
            ->assertDontSee('<i class="fa-regular fa-star"></i>', false);

        //お気に入り解除
        $response = $this->post(route('favorites.favorite'), ['item_id' => $item->id]);
        $response->assertStatus(302);

        //DB から削除
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('<i class="fa-regular fa-star"></i>', false)
            ->assertDontSee('<i class="fa-solid fa-star"></i>', false)
            ->assertSee('<p>0</p>', false);
    }

    //追加済みのアイコンは色が変化する
    public function test_favorite_icon_color() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. 商品詳細ページを開く
        $response = $this->post(route('favorites.favorite'),[
            'item_id' => $item->id,
        ]);

        //3. いいねアイコンを押下 
        $response->assertStatus(302);

        //いいねアイコンが押下された状態では色が変化する
        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertSee('<i class="fa-solid fa-star"></i>', false);
    }
}
