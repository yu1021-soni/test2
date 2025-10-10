<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CategorySeeder;


class SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //「商品名」で部分一致検索ができる
    public function test_search_partial() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();

        $matchItem = Item::factory()->create(['name' => 'コーヒーミル']);
        $notMatchItem = Item::factory()->create(['name' => '紅茶カップ']);

        //1. 検索欄にキーワードを入力
        //2. 検索ボタンを押す
        $response = $this->get(route('items.search', ['keyword' => 'コーヒー']));

        $response->assertOk();

        //部分一致する商品が表示される
        $response->assertSeeText('コーヒーミル');
        $response->assertDontSeeText('紅茶カップ');
    }

    //検索状態がマイリストでも保持されている
    public function test_search_keep_mylist() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();

        $Item = Item::factory()->create(['name' => 'コーヒーミル']);

        // ログイン状態
        $this->actingAs($user);

        //1. ホームページで商品を検索
        $response = $this->get(route('items.search', ['keyword' => 'コーヒー']));

        //2. 検索結果が表示される
        $response->assertSeeText('コーヒーミル');
        $response->assertSee('value="コーヒー"',false);

        //3. マイリストページに遷移
        $response = $this->get(route('item.index', ['tab' => 'mylist', 'keyword' => 'コーヒー']));
        
        //検索キーワードが保持されている
        $response->assertOk();
        $response->assertSee('value="コーヒー"', false);
    }
}
