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
            'detected_content' => function () {
                $data = json_decode(file_get_contents(base_path('tests/Fixtures/parsed-resume-sample.json')), true);

                return $data['markdown_content'];
            },
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
