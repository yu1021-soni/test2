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


class DetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //必要な情報が表示される
    public function test_detail_display() {

        //カテゴリ一覧をDBに
        $this->seed(CategorySeeder::class);

        //テスト用ユーザー
        $user = User::factory()->create(['name' => '山田 太郎']);

        //Seederで入れたカテゴリをDBから取得
        $categoryId = DB::table('categories')->where('name', 'ファッション')->value('id');

        //categoriesテーブルから name='ファッション'を探す
        $category = Category::findOrFail($categoryId);

        // 商品作成
        $item = Item::factory()->create([
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'price'       => 1000,
            'description' => 'テスト説明文',
            'condition'   => 1,
        ]);

        $item->categories()->attach($categoryId);

        // いいね1件
        $user->favorites()->attach($item->id);

        // コメント1件
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);

        // 詳細ページへ
        $response = $this->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertOk();

        // 画像
        $response->assertSee('alt="'.$item->name.'"', false);

        // 基本情報
        $response->assertSeeText($item->name);
        $response->assertSeeText($item->brand);
        $response->assertSee('¥' . number_format($item->price), false);
        $response->assertSeeText($item->description);

        $response->assertSeeText('カテゴリー');
        $response->assertSeeText($category->name);

        // 状態ラベル
        $response->assertSeeText('良好');

        // いいね数・コメント数（1件ずつ想定で「1」を含む）
        $response->assertSeeText('1');

        // コメントしたユーザー名・内容
        $response->assertSeeText('山田 太郎');
        $response->assertSeeText('テストコメント');
    }

    //複数選択されたカテゴリが表示されているか
    public function test_detail_categories() {

        $this->seed(CategorySeeder::class);

        //テスト用商品
        $item = Item::factory()->create();

        $categories = Category::whereIn('name', ['ファッション', 'インテリア'])->get();
        //商品にカテゴリ紐付け
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertOk();

        foreach ($categories as $category) {
            $response->assertSeeText($category->name);
        }
    }
}