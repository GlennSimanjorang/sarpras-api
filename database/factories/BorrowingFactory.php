<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "item_id" => Item::query()->inRandomOrder()->first()->id,
            "user_id" => User::query()->inRandomOrder()->first()->id,
            "quantity" => fake()->numberBetween(1, 100)
        ];
    }
}
