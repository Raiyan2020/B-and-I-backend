<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => [
                'ar' => $this->faker->words(2, true),
                'en' => $this->faker->words(2, true),
            ],
            'status' => true,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => false,
            ];
        });
    }
}
