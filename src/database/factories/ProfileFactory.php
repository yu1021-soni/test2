<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'      => User::factory(),
            'user_img_url' => 'avatars/test.png',
            'postcode'     => '111-1111',
            'address'      => 'テスト住所',
            'building'     => 'テスト建物',
        ];
    }
}
