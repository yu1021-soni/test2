<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_login_email_require() {
        $response = $this->from('/login')->post('/login',[
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_password_required() {
        $response = $this->from('/login')->post('/login', [
            'email' => 'taro@example.com',
            'password' => '',
        ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['password']);
    }

    public function test_login_input_information_error() {
        $response = $this->from('/login')->post('/login', [
            'email' => 'aaa@aaa.com',
            'password' => 'aaaaaaaa',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_successful() {

        //失敗->未入力・不一致は ユーザー不要でも通る
        //成功-> User をfactoryで事前作成が必要
        $user = User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => 'password123',
        ]);

        // ログイン済みユーザーが $user と一致
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }
}
