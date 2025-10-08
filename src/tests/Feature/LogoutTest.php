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
    
    public function test_logout_successful() {

        //テスト用のユーザーを自動生成
        $user = User::factory()->create();

        //ログイン
        $this->actingAs($user);

        //ログアウトリクエスト
        $response = $this->post('/logout');

        //未ログインアサーション
        $this->assertGuest();

        $response->assertRedirect('/login');
    }
}
