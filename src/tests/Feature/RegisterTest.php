<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //array 配列を受け取る
    //$override = []  何も渡されなかったら空の配列
    public function validPayload(array $override=[]):array{
        return array_merge([
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $override);
    }

    //名前未入力
    public function test_name_is_required() {
        $response = $this->from(route('register'))
            ->post(route('register'),$this->validPayload(['name' => '']));

        $response->assertRedirect(route('register')); //エラー時に戻るページ
        $response->assertSessionHasErrors('name'); //
    }

    //メール未入力
    public function test_email_is_required() {
        $response = $this->from(route('register'))
            ->post(route('register'),$this->validPayload(['email' => '']));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
    }

    //パスワード未入力
    public function test_password_is_required() {
        $response = $this->from(route('register'))
            ->post(route('register'),$this->validPayload([
                'password' => '',
                'password_confirmation' => '',
            ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
    }

    //パスワード7文字以下
    public function test_password_must_be_at_least_8_chars() {
        $response = $this->from(route('register'))
            ->post(route('register'), $this->validPayload([
                'password' => '1111',
                'password_confirmation' => '1111',
            ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
    }

    //パスワード不一致
    public function test_password_confirmation_must_match() {
        $response = $this->from(route('register'))
            ->post(route('register'), $this->validPayload([
                'password_confirmation' => 'DIFFERENT',
            ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
    }

    //登録後プロフィール編集画面に移動
    public function test_register_success_and_redirect_to_profile_edit() {
        $response = $this->post(route('register'),$this->validPayload());

        //usersテーブルにメールが保存されたことを確認
        $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);
        $response->assertRedirect(route('profile.edit'));
    }

    //クラスの中のメソッドから、自分のメソッドや機能を呼ぶときに $this->を使用
}
