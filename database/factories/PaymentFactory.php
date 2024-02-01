<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participation_Tontine_id' => $this->faker->numberBetween(1, 10),
            'gestion_cycle_id' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'token' => $this->faker->unique()->numberBetween(1, 10),
        ];
    }
}
