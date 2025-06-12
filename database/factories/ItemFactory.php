<?php

namespace Database\Factories;

use App\Utility\Formatter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word();
        $sku = Formatter::removeVowels($name);
        return [
            "sku" => $sku,
            "name" => $name,
            "stock" => fake()->numberBetween(0, 100)
        ];
    }
}
