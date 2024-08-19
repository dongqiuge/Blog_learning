<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date_time = $this->faker->date . ' ' . $this->faker->time;
        return [
            'user_id' => $this->faker->randomElement(['1', '2', '3']),
            'content' => $this->faker->text(),
            'created_at' => $date_time,
            'updated_at' => $date_time
        ];
    }
}
