<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_id' => null, // Should be set when creating
            'title_ar' => $this->faker->sentence(),
            'title_en' => $this->faker->sentence(),
            'body_ar' => $this->faker->paragraph(),
            'body_en' => $this->faker->paragraph(),
            'seen' => 0,
        ];
    }

    /**
     * Indicate that the notification is seen.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function seen()
    {
        return $this->state(function (array $attributes) {
            return [
                'seen' => 1,
            ];
        });
    }
}
