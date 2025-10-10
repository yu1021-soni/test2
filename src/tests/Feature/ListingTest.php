<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Database\Seeders\CategorySeeder;

class ListingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //商品出品画面にて必要な情報が保存できること
    public function test_listing() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $category = Category::first();

        // ストレージをフェイク
        Storage::fake('public');

        // 1x1 PNG（GD不要）
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/axrZxQAAAAASUVORK5CYII='
        );
        $tmp = tempnam(sys_get_temp_dir(), 'png');
        file_put_contents($tmp, $png);

        // Symfonyの UploadedFile を生成
        $file = new UploadedFile($tmp, 'item.png', 'image/png', null, true);

        //ユーザーにログイン
        $this->actingAs($user);

        //各項目に適切な情報を入力して保存する" 各項目が正しく保存されている
        $response = $this->post(route('item.sell'), [
            'item_img_url' => $file,          // ダミー画像
            'categories'   => [$category->id],
            'condition'    => 1,
            'name'         => 'テスト商品',
            'description'  => 'テスト説明文',
            'price'        => 1000,
        ]);

        $response->assertSessionHasNoErrors()->assertStatus(302);

        //各項目が正しく保存されている確認
        $this->assertDatabaseHas('items', [
            'user_id'     => $user->id,
            'name'        => 'テスト商品',
            'description' => 'テスト説明文',
            'price'       => 1000,
        ]);
    }
}
