<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->sentence(2),
            'sku' => str()->random(),
            'price' => fake()->randomNumber(5),
            'available_quantity' => fake()->randomNumber(2),
            'description' => fake()->sentence(),
        ];
    }
}
