<?php

use App\Models\Optimization;
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
    $this->assertDatabaseHas(Resume::class, [
        'id' => $resume->id,
        'deleted_at' => now(),
    ]);
});

it('does not allow a user to delete a resume that does not belong to them', function () {
    $resume = Resume::factory()->create();
    $anotherUser = User::factory()->create();

    $this->withToken($anotherUser->api_token)
        ->deleteJson("/api/resumes/{$resume->id}")
        ->assertForbidden();
});

it('handles deleted resumes when parsing optimization', function () {
    $resume = Resume::factory()->create();
    $optimization = Optimization::factory()->create(['resume_id' => $resume->id, 'user_id' => $resume->user_id]);

    $resume->delete();

    tap($optimization->fresh(), function (Optimization $optimization) {
        expect($optimization->resume)->not->toBeNull();
    });
});
