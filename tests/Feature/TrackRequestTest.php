<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_index_page_can_be_rendered(): void
    {
        $response = $this->get('/track');
        $response->assertStatus(200);
        $response->assertSee('Track Your Requests');
    }

    public function test_track_search_invalid_reference_or_nonexistent_email(): void
    {
        $response = $this->post('/track', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertSee('No requests found');

        $response2 = $this->post('/track', [
            'email' => 'SK-REQ-99999',
        ]);

        $response2->assertStatus(200);
        $response2->assertSee('No requests found');
    }

    public function test_track_search_validation(): void
    {
        $response = $this->post('/track', [
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
