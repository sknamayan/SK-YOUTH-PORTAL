<?php
 
namespace Tests\Feature;
 
use App\Models\ConsultationRequest;
use App\Models\ComplaintMessage;
use App\Models\User;
use App\Models\Purok;
use App\Models\KkProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
class ConsultationTest extends TestCase
{
    use RefreshDatabase;
 
    private function createProfiledUser(): User
    {
        $user = User::factory()->create(['role' => 'user']);
 
        // Seed default Purok
        $purok = Purok::firstOrCreate([
            'id' => 1
        ], [
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);
 
        // Create approved KK profile
        KkProfile::create([
            'surname' => 'Doe',
            'first_name' => 'Jane',
            'age' => 20,
            'sex' => 'Female',
            'dob' => '2006-05-20',
            'civil_status' => 'Single',
            'purok_id' => $purok->id,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => $user->email,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'status' => 'approved',
        ]);
 
        return $user;
    }
 
    /**
     * Test guest redirects.
     */
    public function test_guest_cannot_access_consultation_routes(): void
    {
        $response1 = $this->get(route('skonsulta.index'));
        $response1->assertRedirect('/login');
 
        $response2 = $this->postJson(route('consultations.store'), [
            'category' => 'General Concern',
            'subject' => 'Subject',
            'message' => 'Message body',
        ]);
        $response2->assertStatus(401); // Unauthorized
    }
 
    /**
     * Test citizen without KK profile cannot access skonsulta.
     */
    public function test_citizen_without_kk_profile_cannot_access_skonsulta(): void
    {
        $user = User::factory()->create(['role' => 'user']);
 
        $response = $this->actingAs($user)->get(route('skonsulta.index'));
        $response->assertRedirect(route('profile.profiling.create'));
        $response->assertSessionHas('error');
    }
 
    /**
     * Test profiled citizen can view skonsulta index.
     */
    public function test_profiled_citizen_can_view_skonsulta_index(): void
    {
        $user = $this->createProfiledUser();
 
        $response = $this->actingAs($user)->get(route('skonsulta.index'));
        $response->assertRedirect(route('profile.my-requests', ['skonsulta' => 'open']));
    }
 
    /**
     * Test citizen can create a consultation complaint thread.
     */
    public function test_citizen_can_submit_complaint_thread(): void
    {
        $user = $this->createProfiledUser();
 
        $response = $this->actingAs($user)->postJson(route('consultations.store'), [
            'category' => 'Report',
            'subject' => 'Unlit Street Lights',
            'message' => 'The street lamps in Purok 1 have been broken for two weeks.',
        ]);
 
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'consultation_id',
            'tracking_id',
        ]);
 
        $data = $response->json();
        $this->assertDatabaseHas('consultation_requests', [
            'id' => $data['consultation_id'],
            'user_id' => $user->id,
            'category' => 'Report',
            'subject' => 'Unlit Street Lights',
            'status' => 'Open',
        ]);
 
        // Initial message must be stored in the messages table
        $this->assertDatabaseHas('complaint_messages', [
            'consultation_request_id' => $data['consultation_id'],
            'sender_id' => $user->id,
            'body' => 'The street lamps in Purok 1 have been broken for two weeks.',
        ]);
    }
 
    /**
     * Test citizen can view own thread and messages.
     */
    public function test_citizen_can_view_own_thread_and_messages(): void
    {
        $user = $this->createProfiledUser();
        $consultation = ConsultationRequest::create([
            'user_id' => $user->id,
            'category' => 'Suggestion',
            'subject' => 'Youth Park Renovation',
            'message' => 'We need more plants and benches.',
            'status' => 'Open',
        ]);
        
        $message = ComplaintMessage::create([
            'consultation_request_id' => $consultation->id,
            'sender_id' => $user->id,
            'body' => 'We need more plants and benches.',
        ]);
 
        $responseShow = $this->actingAs($user)->get(route('skonsulta.show', $consultation));
        $responseShow->assertRedirect(route('profile.my-requests', [
            'skonsulta' => 'open',
            'thread_id' => $consultation->id
        ]));
 
        $responseMessages = $this->actingAs($user)->getJson(route('skonsulta.messages', $consultation));
        $responseMessages->assertStatus(200);
        $responseMessages->assertJsonFragment([
            'body' => 'We need more plants and benches.',
            'sender_name' => $user->name,
        ]);
    }
 
    /**
     * Test citizen cannot access others' threads.
     */
    public function test_citizen_cannot_access_others_threads(): void
    {
        $citizen1 = $this->createProfiledUser();
        $citizen2 = $this->createProfiledUser();
 
        $consultation = ConsultationRequest::create([
            'user_id' => $citizen1->id,
            'category' => 'Suggestion',
            'subject' => 'Citizen 1 Subject',
            'message' => 'Citizen 1 message',
            'status' => 'Open',
        ]);
 
        $responseShow = $this->actingAs($citizen2)->get(route('skonsulta.show', $consultation));
        $responseShow->assertStatus(403);
 
        $responseMessages = $this->actingAs($citizen2)->getJson(route('skonsulta.messages', $consultation));
        $responseMessages->assertStatus(403);
 
        $responseSend = $this->actingAs($citizen2)->postJson(route('skonsulta.send-message', $consultation), [
            'body' => 'Intruder reply',
        ]);
        $responseSend->assertStatus(403);
    }
 
    /**
     * Test staff/admin can view all threads.
     */
    public function test_staff_can_view_all_threads(): void
    {
        $citizen = $this->createProfiledUser();
        $staff = User::factory()->create(['role' => 'staff']);
 
        $consultation = ConsultationRequest::create([
            'user_id' => $citizen->id,
            'category' => 'Suggestion',
            'subject' => 'Citizen Subject',
            'message' => 'Citizen message',
            'status' => 'Open',
        ]);
 
        $responseIndex = $this->actingAs($staff)->get(route('admin.consultations.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Citizen Subject');
 
        $responseShow = $this->actingAs($staff)->get(route('admin.consultations.show', $consultation));
        $responseShow->assertStatus(200);
 
        $responseMessages = $this->actingAs($staff)->getJson(route('skonsulta.messages', $consultation));
        $responseMessages->assertStatus(200);
    }
 
    /**
     * Test sending new messages in the thread.
     */
    public function test_user_can_send_message_in_chat(): void
    {
        $citizen = $this->createProfiledUser();
        $consultation = ConsultationRequest::create([
            'user_id' => $citizen->id,
            'category' => 'Suggestion',
            'subject' => 'Subject',
            'message' => 'Initial message',
            'status' => 'Open',
        ]);
 
        $response = $this->actingAs($citizen)->postJson(route('skonsulta.send-message', $consultation), [
            'body' => 'Adding a follow-up reply in chat.',
        ]);
 
        $response->assertStatus(200);
        $this->assertDatabaseHas('complaint_messages', [
            'consultation_request_id' => $consultation->id,
            'sender_id' => $citizen->id,
            'body' => 'Adding a follow-up reply in chat.',
        ]);
    }
 
    /**
     * Test staff can update consultation status.
     */
    public function test_staff_can_update_consultation_status(): void
    {
        $citizen = $this->createProfiledUser();
        $staff = User::factory()->create(['role' => 'staff']);
        
        $consultation = ConsultationRequest::create([
            'user_id' => $citizen->id,
            'category' => 'Report',
            'subject' => 'Loud Party Complaint',
            'message' => 'Neighbours are hosting a loud karaoke.',
            'status' => 'Open',
        ]);
 
        $response = $this->actingAs($staff)->patch(route('admin.consultations.update-status', $consultation), [
            'status' => 'In Progress',
        ]);
 
        $response->assertRedirect();
        $this->assertDatabaseHas('consultation_requests', [
            'id' => $consultation->id,
            'status' => 'In Progress',
        ]);
    }
 
    /**
     * Test duplicate consultations within 5 minutes cooldown are blocked.
     */
    public function test_submitting_duplicate_consultation_within_cooldown_fails(): void
    {
        $user = $this->createProfiledUser();
 
        // 1. Submit first consultation
        $response1 = $this->actingAs($user)->postJson(route('consultations.store'), [
            'category' => 'General Concern',
            'subject' => 'Duplication Test Subject',
            'message' => 'Duplication test message content.',
        ]);
        $response1->assertStatus(201);
 
        // 2. Submit identical consultation immediately
        $response2 = $this->actingAs($user)->postJson(route('consultations.store'), [
            'category' => 'General Concern',
            'subject' => 'Duplication Test Subject',
            'message' => 'Duplication test message content.',
        ]);
        
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['message']);
    }

    public function test_citizen_can_fetch_requests_for_follow_up(): void
    {
        $user = $this->createProfiledUser();

        ConsultationRequest::create([
            'user_id' => $user->id,
            'category' => 'General Concern',
            'subject' => 'General Inquiry',
            'message' => 'Fever and cold symptoms',
            'status' => 'Open'
        ]);

        $response = $this->actingAs($user)->getJson(route('skonsulta.api.citizen-requests'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'requests' => [
                '*' => ['id', 'ref', 'type', 'title', 'status']
            ]
        ]);
        $response->assertJsonFragment([
            'type' => 'Consultation Request',
            'status' => 'Open'
        ]);
    }
}

