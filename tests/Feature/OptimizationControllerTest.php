<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OptimizationControllerTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    function it_creates_a_new_optimization_when_the_role_information_is_sent()
    {
        $response = $this->withHeader('X-CurrentStep', 0)
            ->actingAs($user = User::factory()->create())
            ->post('/optimizations/create', [
                'name' => 'Backend Engineer',
                'company' => 'Laravel',
                'description' => 'Lore Ipsum!',
                'url' => 'https://www.linkedin.com/jobs/view/4253350439/'
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseCount('optimizations', 1);
        $this->assertDatabaseHas('optimizations', [
            'role_name' => 'Backend Engineer',
            'role_company' => 'Laravel',
            'role_description' => 'Lore Ipsum!',
            'current_step' => '1',
            'user_id' => $user->id,
        ]);

        $this->assertSame($response->json('step'), 0);
        $this->assertSame($response->json('optimization.role_name'), 'Backend Engineer');
    }

    #[Test]
    function it_sets_an_existing_resume_in_the_optimization()
    {
        $optimization = \App\Models\Optimization::factory()->create([
            'resume_id' => null,
        ]);
        $resume = \App\Models\Resume::factory()->create();
        $response = $this->withHeader('X-CurrentStep', 1)
            ->actingAs($optimization->user)
            ->put(route('optimizations.update', $optimization), [
                'id' => $resume->id,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseCount('optimizations', 1);
        $this->assertDatabaseHas('optimizations', [
            'id' => $optimization->id,
            'current_step' => '2',
            'user_id' => $optimization->user_id,
            'resume_id' => $resume->id,
        ]);

        $this->assertTrue($optimization->fresh()->resume->is($resume));
    }

    #[Test]
    function an_optimized_resume_can_be_downloaded()
    {
        // an optimized resume can be downloaded
        $optimization = \App\Models\Optimization::factory()->create([
            'optimized_result' => '<h1>John doe</h1><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto cum doloribus necessitatibus praesentium quae quis sunt, voluptates. Cumque eos esse, ex facere, in maiores nobis obcaecati omnis placeat, recusandae veritatis!</p>'
        ]);

        $response = $this->withToken($optimization->user->api_token)->post(route('optimizations.download', $optimization));

        $response->assertSuccessful();

        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertSame('attachment; filename="'.$optimization->optimizedResumeFileName().'"', $response->headers->get('content-disposition'));
    }

    #[Test]
    function a_cover_letter_can_be_downloaded()
    {
        // an optimized resume can be downloaded
        $optimization = \App\Models\Optimization::factory()->create([
            'ai_response' => [
                'cover_letter' => [
                    '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aliquam animi architecto at dicta distinctio, eius est eveniet labore magni nobis quisquam sapiente! Accusantium consequatur dicta fuga laudantium non rem.</p>'
                ],
            ]
        ]);

        $response = $this->withToken($optimization->user->api_token)->post(route('optimizations.download-cover', $optimization));

        $response->assertSuccessful();

        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertSame('attachment; filename="'.$optimization->coverLetterFileName().'"', $response->headers->get('content-disposition'));
    }

    #[Test]
    function it_can_cancel_an_edit_and_restore_the_optimization()
    {
        $optimization = \App\Models\Optimization::factory()->create([
            'status' => 'draft',
            'current_step' => 1,
            'ai_response' => ['compatibility_score' => 99],
        ]);

        $response = $this->actingAs($optimization->user)
            ->put(route('optimizations.cancel', $optimization));

        $response->assertSuccessful();

        $optimization->refresh();

        $this->assertSame('complete', $optimization->status);
        $this->assertSame(3, $optimization->current_step);
    }
}
