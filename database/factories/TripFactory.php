<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Car;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'miles' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 10000),
            'total' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 10000),
            'car_id' => Car::factory()
        ];
    }
}
