<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('the system can parse a resume contents and return useful information', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $file = UploadedFile::fake()->createWithContent(
        'resume.pdf',
        file_get_contents(realpath(__DIR__ . '/../Fixtures/resume-example.pdf'))
    );
    $path = "{$user->id}/resumes";
    $file->storeAs($path, $file->hashName(), 'local');

    $resume = Resume::factory([
        'user_id' => $user->id,
    ])->forFile($file)->create();

    $response = $this->withToken($user->api_token)->postJson("/api/parser/{$resume->id}");

    $response->assertSuccessful();

    $this->assertStringContainsString('Eduardo Fleck Dalla Vecchia', $response->json('content'));
});
