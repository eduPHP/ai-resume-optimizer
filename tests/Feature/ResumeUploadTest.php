<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a user can upload a resume', function () {
    Storage::fake('local');
    $file = UploadedFile::fake()->createWithContent('resume.pdf', 'Some resume content');

    $user = User::factory()->create();
    $response = $this->withToken($user->api_token)->postJson('/api/resume', [
        'resume' => $file,
    ]);

    $response->assertSuccessful();

    Storage::assertExists($response->json('resume.path'));
    $this->assertDatabaseCount(Resume::class, 1);
    $this->assertDatabaseHas(Resume::class, [
        'name' => 'resume.pdf',
        'user_id' => $user->id,
    ]);
});



