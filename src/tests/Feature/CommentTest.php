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

        //1. ユーザーにログインする
        $this->actingAs($user);

        $this->get(route('items.detail', ['item_id' => $item->id]))
            ->assertOk()
            ->assertSee('コメント (0)');

        //2. コメントを入力する
        //3. コメントボタンを押す
        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
        $response->assertStatus(302);

        //コメントが保存され、コメント数が増加する
        $response = $this->get(route('items.detail', ['item_id' => $item->id]));
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
        $response->assertSee('<p>1</p>', false);
        $response->assertSeeText('テストコメント');
        $response->assertSeeText($user->name);
    }

    //ログイン前のユーザーはコメントを送信できない
    public function test_comment_logout_not_send() {

        $this->seed(CategorySeeder::class);

        $item = Item::factory()->create();

        //1. コメントを入力する
        //2. コメントボタンを押す
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

    //コメントが入力されていない場合、バリデーションメッセージが表示される
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

    //コメントが255字以上の場合、バリデーションメッセージが表示される
    public function test_comment_number_error() {

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $item = Item::factory()->create();

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. 255文字以上のコメントを入力する
        $LongComment = str_repeat('a', 256);

        //3. コメントボタンを押す
        $response = $this->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => $LongComment,
        ]);

        //バリデーションメッセージが表示される
        $response->assertSessionHasErrors(['comment']);
    }
}