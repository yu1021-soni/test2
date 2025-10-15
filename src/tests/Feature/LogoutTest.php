<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //ログアウトができる
    public function test_logout_successful() {

        //テスト用のユーザーを自動生成
        $user = User::factory()->create();

        //1. ユーザーにログインをする
        $this->actingAs($user);

        //2. ログアウトボタンを押す
        $response = $this->post('/logout');

        //未ログインアサーション
        $this->assertGuest();

        $response->assertRedirect('/login');
    }
}
