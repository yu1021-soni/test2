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

    public function test_favorite_register () {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('favorites.favorite'),[
            'item_id' => $item->id,
        ]);

        //お気に入り登録の時よく使う
        $response->assertStatus(302);

        // DBに登録されたか
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_favorite_not_register () {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('favorites.favorite'),[
            'item_id' => $item->id,
        ]);

        //お気に入り登録の時よく使う
        $response->assertStatus(302);

        // DBに登録されたか
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);


        //お気に入り解除
        $response = $this->post(route('favorites.favorite'),[
            'item_id' => $item->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    //いいねアイコンの色が変わる
    public function test_favorite_icon_color() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // いいね
        $response = $this->post(route('favorites.favorite'),[
            'item_id' => $item->id,
        ]);

        $response->assertStatus(302);

        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertSee('<i class="fa-solid fa-star"></i>', false);
    }
}
