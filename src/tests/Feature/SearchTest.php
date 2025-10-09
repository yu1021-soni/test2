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

    //部分検索機能
    public function test_search_partial() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();

        $matchItem = Item::factory()->create(['name' => 'コーヒーミル']);
        $notMatchItem = Item::factory()->create(['name' => '紅茶カップ']);

        //部分検索
        $response = $this->get('/search?keyword=コーヒー');

        $response->assertOk();
        $response->assertSeeText('コーヒーミル');
        $response->assertDontSeeText('紅茶カップ');
    }

    //検索状態がmylistでも保持
    public function test_search_keep_mylist() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();

        $Item = Item::factory()->create(['name' => 'コーヒーミル']);

        // ログイン状態で「コーヒー」で検索
        $this->actingAs($user);

        // マイリストタブに遷移
        $response = $this->get('/?tab=mylist&keyword=コーヒー');
        
        $response->assertOk();
        $response->assertSee('コーヒー');
    }
}
