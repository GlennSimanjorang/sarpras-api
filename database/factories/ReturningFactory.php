<?php

namespace Database\Factories;

use App\Models\Borrowing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Returning>
 */
class ReturningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "borrow_id" => Borrowing::query()->first()->id,
            "returned_quantity" => fake()->numberBetween(1, 100)
        ];
    }
}
