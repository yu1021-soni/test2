<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), //購入者
            'item_id' => Item::factory(), //購入商品
            'payment'  => 2,
            'postcode' => '150-0001',
            'address'  => '東京都渋谷区神宮前1-1-1',
            'building' => 'テストビル101',
        ];
    }
}
