<?php

namespace Database\Factories;

use App\Enums\OptimizationStatuses;
use App\Models\Resume;
use App\Models\User;
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
        $step = fake()->numberBetween(0, 3);
        return [
            'user_id' => User::factory(),
            'role_name' => fake()->randomElement(['Developer', 'Designer', 'Manager', 'Engineer', 'Tester']),
            'role_url' => fake()->url(),
            'role_company' => fake()->company(),
            'role_description' => fake()->sentence(),
            'resume_id' => Resume::factory(),
            'role_location' => fake()->city(),
            'current_step' => $step,
            'make_grammatical_corrections' => fake()->boolean(),
            'change_professional_summary' => fake()->boolean(),
            'generate_cover_letter' => fake()->boolean(),
            'change_target_role' => fake()->boolean(),
            'mention_relocation_availability' => fake()->boolean(),
            'status' => $step === 3 ? OptimizationStatuses::Complete : OptimizationStatuses::Draft,
            'optimized_result' => fake()->randomHtml(),
            'ai_response' => [
                "resume" => fake()->randomHtml(),
                "compatibility_score" => fake()->numberBetween(50, 100),
                "top_choice" => fake()->sentence(),
                "professional_summary" => fake()->paragraph(),
                "cover_letter" => [
                    fake()->sentence(),
                    fake()->sentence(),
                    fake()->sentence(),
                ],
                "findings" => [
                    [
                        "title" => fake()->sentence(),
                        "description" => fake()->sentence(),
                        "group" => 'strong_alignment',
                    ],
                    [
                        "title" => fake()->sentence(),
                        "description" => fake()->sentence(),
                        "group" => 'strong_alignment',
                    ],
                    [
                        "title" => fake()->sentence(),
                        "description" => fake()->sentence(),
                        "group" => 'moderate_gap',
                    ],
                    [
                        "title" => fake()->sentence(),
                        "description" => fake()->sentence(),
                        "group" => 'moderate_gap',
                    ],
                    [
                        "title" => fake()->sentence(),
                        "description" => fake()->sentence(),
                        "group" => 'missing_requirement',
                    ],
                ],
                "reasoning" => fake()->paragraph(),
            ],
            'reasoning' => fake()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function randomCreationDates(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => now()->subDays(rand(0, 4)),
            'updated_at' => now()->subDays(rand(0, 4)),
        ]);
    }
}
