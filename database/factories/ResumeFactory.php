<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResumeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(2),
            'type' => 'application/pdf',
            'size' => fn() => random_int(1234, 4333),
            'path' => $this->faker->filePath(),
            'detected_content' => $this->faker->text(),
        ];
    }

    public function forFile($file): static
    {
        return $this->state(fn (array $attributes) =>  [
            'name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $attributes['user_id'] . '/resumes/' . $file->hashName(),
        ]);
    }
}
