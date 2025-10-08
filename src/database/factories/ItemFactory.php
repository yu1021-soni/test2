<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id'  =>  Category::inRandomOrder()->first()->id,
            'name' => $this->faker->word,
            'item_img_url' => 'items/sample.jpg',
            'price' => $this->faker->numberBetween(1,4294967295),
            'description' => $this->faker->paragraph(),
            'condition' => $this->faker->randomElement([1,2,3,4]),
            'brand' => $this->faker->optional()->word(),
            //optional nullでもok
        ];
    }
}
