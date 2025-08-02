<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows a user to delete a resume', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('resume.pdf', 100);
    $path = $file->store('resumes', 'local');

    $resume = $user->resumes()->create([
        'name' => 'resume.pdf',
        'type' => 'application/pdf',
        'size' => $file->getSize(),
        'path' => $path,
        'detected_content' => 'dummy',
    ]);

    Storage::assertExists($path);

    $this->withToken($user->api_token)
        ->deleteJson("/api/resumes/{$resume->id}")
        ->assertNoContent();

    Storage::assertMissing($path);
    $this->assertDatabaseMissing(Resume::class, [
        'id' => $resume->id,
    ]);
});
