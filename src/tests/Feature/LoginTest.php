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

    //メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_login_email_require() {

        //1. ログインページを開く
        $response = $this->from('/login')
            //2. メールアドレスを入力せずに他の必要項目を入力する
            ->post('/login',[
                'email' => '',
                'password' => 'password123',
        ]);

        //3. ログインボタンを押す
        $response->assertRedirect('/login');

        //「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors(['email']);
    }

    //パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_login_password_required() {

        //1. ログインページを開く
        $response = $this->from('/login')
            //2. パスワードを入力せずに他の必要項目を入力する
            ->post('/login', [
                'email' => 'taro@example.com',
                'password' => '',
            ]);

    //3. ログインボタンを押す
    $response->assertRedirect('/login');

    //「パスワードを入力してください」というバリデーションメッセージが表示される
    $response->assertSessionHasErrors(['password']);
    }

    //入力情報が間違っている場合、バリデーションメッセージが表示される
    public function test_login_input_information_error() {

        //1. ログインページを開く
        $response = $this->from('/login')
            //2. 必要項目を登録されていない情報を入力する 
            ->post('/login', [
                'email' => 'aaa@aaa.com',
                'password' => 'aaaaaaaa',
            ]);

        //3. ログインボタンを押す
        $response->assertRedirect('/login');

        //「ログイン情報が登録されていません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors(['email']);
    }

    //正しい情報が入力された場合、ログイン処理が実行される
    public function test_login_successful() {

        //失敗->未入力・不一致は ユーザー不要でも通る
        //成功-> User をfactoryで事前作成が必要

        $user = User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        //1. ログインページを開く
        $this->get('/login')->assertOk();

        //2. 全ての必要項目を入力する
        //3. ログインボタンを押す
        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => 'password123',
        ]);

        // ログイン済みユーザーが $user と一致
        //ログイン処理が実行される
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }
}
