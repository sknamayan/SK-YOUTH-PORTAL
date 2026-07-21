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

    public function test_track_search_by_email_fails_validation(): void
    {
        // Searching with an email parameter should fail validation as reference_number is now required
        $response = $this->post('/track', [
            'email' => 'user@example.com',
        ]);

        $response->assertSessionHasErrors('reference_number');
    }

    public function test_track_search_by_reference_number(): void
    {
        $response = $this->post('/track', [
            'reference_number' => 'SK-REQ-99999',
        ]);

        $response->assertStatus(200);
        $response->assertSee('No requests found');
        $response->assertSee('SK-REQ-99999');
    }

    public function test_track_search_validation(): void
    {
        $response = $this->post('/track', [
            'reference_number' => '',
        ]);

        $response->assertSessionHasErrors('reference_number');
    }
}
