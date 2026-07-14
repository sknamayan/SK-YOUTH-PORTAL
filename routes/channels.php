<?php
 
use Illuminate\Support\Facades\Broadcast;
use App\Models\ConsultationRequest;
 
Broadcast::channel('consultation.{id}', function ($user, $id) {
    $consultation = ConsultationRequest::find($id);
    if (!$consultation) {
        return false;
    }
 
    // Allow the citizen who created the thread, or any SK Official who can access the dashboard.
    return (int) $user->id === (int) $consultation->user_id || $user->canAccessDashboard();
});
