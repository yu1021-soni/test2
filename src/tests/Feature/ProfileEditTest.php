<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class ProfileEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //変更項目が初期値として過去設定されていること
    public function test_profile_edit() {

        $user = User::factory()->create([
            'name' => 'テスト名前',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'user_img_url' => 'avatars/test.png',
            'postcode' => '111-1111',
            'address' => 'テスト住所',
        ]);

        //1. ユーザーにログインする
        $this->actingAs($user);

        //2. プロフィールページを開く
        $response = $this->get(route('profile.view'));
        $response->assertOk();

        //各項目の初期値が正しく表示されている
        $response->assertSee('/storage/avatars/test.png');
        $response->assertSee('111-1111');
        $response->assertSee('テスト住所');
    }
}