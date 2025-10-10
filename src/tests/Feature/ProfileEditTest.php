<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ProfileEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_profile_edit() {

        $user = User::factory()->create([
            'name'        => 'テスト名前',
            'user_img_url'=> 'avatars/test.png',
            'postcode'    => '111-1111',
            'address'     => 'テスト住所',
        ]);


        $this->actingAs($user);

        // プロフィール編集ページを開く
        $response = $this->get(route('profile.view'));
        $response->assertOk();

        $response->assertSee('avatars/test.png');
        $response->assertSee('111-1111');
        $response->assertSee('テスト住所');
    }
}
