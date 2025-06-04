<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a user can upload a resume', function () {
    Storage::fake('local');
    $file = UploadedFile::fake()->createWithContent(
        'resume.pdf',
        file_get_contents(realpath(__DIR__ . '/../Fixtures/resume-example.pdf'))
    );

    $user = User::factory()->create();
    $response = $this->withoutExceptionHandling()->withToken($user->api_token)->postJson('/api/resumes', [
        'upload' => $file,
    ]);

    $response->assertSuccessful();

    Storage::assertExists($response->json('resume.path'));
    $this->assertDatabaseCount(Resume::class, 1);
    $this->assertDatabaseHas(Resume::class, [
        'name' => 'resume.pdf',
        'user_id' => $user->id,
    ]);
});



