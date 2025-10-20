<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    //名前が入力されていない場合、バリデーションメッセージが表示される
    public function test_name_is_required() {

        //1. 会員登録ページを開く
        $response = $this->from(route('register'))

            //2. 名前を入力せずに他の必要項目を入力する
            ->post(route('register'),$this->validPayload(['name' => '']));

        //3. 登録ボタンを押す
        $response->assertRedirect(route('register')); //エラー時に戻るページ

        //「お名前を入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors('name');
    }

    //メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_email_is_required() {

        //1. 会員登録ページを開く
        $response = $this->from(route('register'))

            // メールアドレスを入力せずに他の必要項目を入力する
            ->post(route('register'),$this->validPayload(['email' => '']));

        // 登録ボタンを押す
        $response->assertRedirect(route('register'));

        //「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors('email');
    }

    //パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_password_is_required() {

        //1. 会員登録ページを開く
        $response = $this->from(route('register'))

            //2. パスワードを入力せずに他の必要項目を入力する
            ->post(route('register'),$this->validPayload([
                'password' => '',
                'password_confirmation' => '',
            ]));

        //3. 登録ボタンを押す
        $response->assertRedirect(route('register'));

        //「パスワードを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors('password');
    }

    //パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function test_password_must_be_at_least_8_chars() {

        //1. 会員登録ページを開く
        $response = $this->from(route('register'))

            //2. 7文字以下のパスワードと他の必要項目を入力する
            ->post(route('register'), $this->validPayload([
                'password' => '1111',
                'password_confirmation' => '1111',
            ]));

        //3. 登録ボタンを押す
        $response->assertRedirect(route('register'));

        //「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors('password');
    }

    //パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function test_password_confirmation_must_match() {

        //1. 会員登録ページを開く
        $response = $this->from(route('register'))

            //確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する
            ->post(route('register'), $this->validPayload([
                'password_confirmation' => 'DIFFERENT',
            ]));

        //3. 登録ボタンを押す
        $response->assertRedirect(route('register'));

        //「パスワードと一致しません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors('password');
    }

    //全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
    public function test_register_success_and_redirect_to_profile_edit() {

        //1. 会員登録ページを開く（到達確認）
        $this->get(route('register'))->assertOk();

        //2. 全ての必要項目を正しく入力する
        $payload = $this->validPayload();

        //3. 登録ボタンを押す
        $response = $this->from(route('register'))
                    ->post(route('register'), $payload);

        // 入力が正しいのでバリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // DBにユーザーが登録されたことを確認
        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
        ]);

        // プロフィール編集画面にリダイレクトすることを確認
        $response->assertRedirect(route('profile.edit'));
    }

    //クラスの中のメソッドから、自分のメソッドや機能を呼ぶときに $this->を使用
}
