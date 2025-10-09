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

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //ログイン済みのユーザーはコメントを送信できる
    public function test_comment_login_send() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    //ログイン前のユーザーはコメントを送信できない
    public function test_comment_logout_not_send() {

        $this->seed(CategorySeeder::class);

        $item = Item::factory()->create();

        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);

        //ログイン画面へリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        //未ログインのまま
        $this->assertGuest();

        //DBに保存されていない
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    //コメントが入力されていない時 バリデーション
    public function test_comment_require() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);
    }

    //コメントが255文字以上の場合 バリデーション
    public function test_comment_number_error() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $LongComment = str_repeat('a', 256);

        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => $LongComment,
        ]);

        $response->assertSessionHasErrors(['comment']);
    }
}
