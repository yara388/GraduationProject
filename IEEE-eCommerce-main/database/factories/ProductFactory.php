<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @param $faker
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->Text(500),
            'price' => $this->faker->numberBetween(100 , 10000),
            'number_of' => $this->faker->numberBetween(0 , 1000),
            'path' =>  "https://picsum.photos/200/300?random=" . rand(1,50)
        ];
    }
}
