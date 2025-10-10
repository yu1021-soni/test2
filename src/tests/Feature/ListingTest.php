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

    public function test_example() {

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

        $this->actingAs($user);

        $response = $this->post(route('item.sell'), [
            'item_img_url' => $file,          // ダミー画像
            'categories'   => [$category->id],
            'condition'    => 1,
            'name'         => 'テスト商品',
            'description'  => 'テスト説明文',
            'price'        => 1000,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items', [
            'user_id'     => $user->id,
            'name'        => 'テスト商品',
            'description' => 'テスト説明文',
            'price'       => 1000,
        ]);
    }
}
