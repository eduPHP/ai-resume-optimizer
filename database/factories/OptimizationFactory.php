<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Optimization>
 */
class OptimizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_name' => fake()->randomElement(['Developer', 'Designer', 'Manager', 'Engineer']),
            'role_company' => fake()->company,
            'role_description' => fake()->text,
        ];
    }
}
