<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() : array
    {
        return [
            'name' => $this->faker->text(20),
            'description'=> $this->faker->text(255),
            'image'=> $this->faker->imageUrl($width = 200, $height = 200),
            'stock'=> $this->faker->numberBetween(0 , 100),
        ];
    }
}
