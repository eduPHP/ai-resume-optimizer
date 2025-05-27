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
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseCount('optimizations', 1);
        $this->assertDatabaseHas('optimizations', [
            'role_name' => 'Backend Engineer',
            'role_company' => 'Laravel',
            'role_description' => 'Lore Ipsum!',
            'current_step' => '0',
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
            ->put('/optimizations/'.$optimization->id, [
                'id' => $resume->id,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseCount('optimizations', 1);
        $this->assertDatabaseHas('optimizations', [
            'id' => $optimization->id,
            'current_step' => '1',
            'user_id' => $optimization->user_id,
            'resume_id' => $resume->id,
        ]);

        $this->assertTrue($optimization->fresh()->resume->is($resume));
    }
}
