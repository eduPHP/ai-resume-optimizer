<?php

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

    $response = $this->withToken($user->api_token)->postJson("/api/resumes", [
        'upload' => $file,
    ]);

    $response->assertSuccessful();
    $this->assertStringContainsString('Eduardo Fleck Dalla Vecchia', $response->json('content'));
});
