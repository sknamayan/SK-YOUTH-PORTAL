<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use App\Models\ComplaintMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
 
use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\CustomRequest;

class ConsultationController extends Controller
{
    /**
     * Display the SK Official consultations dashboard (two-pane view).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status');
        $search = $request->input('search');
 
        $query = ConsultationRequest::with(['user', 'messages' => function ($q) {
            $q->latest();
        }])->latest();
 
        if ($statusFilter && in_array($statusFilter, ['Open', 'In Progress', 'Resolved'], true)) {
            $query->where('status', $statusFilter);
        }
 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
 
        $consultations = $query->paginate(15)->withQueryString();
        $activeConsultation = null;
 
        return view('admin.consultations.index', compact('consultations', 'statusFilter', 'search', 'activeConsultation'));
    }
 
    /**
     * Display the citizen's own consultations dashboard.
     */
    public function citizenIndex(Request $request): RedirectResponse
    {
        return redirect()->route('profile.my-requests', ['skonsulta' => 'open']);
    }
 
    /**
     * Display the chat room view (for both citizens and officials).
     */
    public function show(Request $request, ConsultationRequest $consultation): View|RedirectResponse
    {
        // Access Control
        if (auth()->user()->id !== $consultation->user_id && !auth()->user()->canAccessDashboard()) {
            abort(403, 'Unauthorized access to this consultation thread.');
        }
 
        if (auth()->user()->role === 'user') {
            return redirect()->route('profile.my-requests', [
                'skonsulta' => 'open',
                'thread_id' => $consultation->id
            ]);
        }
 
        // SK Official view: Loads two-pane with this thread active
        $statusFilter = $request->input('status');
        $search = $request->input('search');
 
        $query = ConsultationRequest::with(['user', 'messages' => function ($q) {
            $q->latest();
        }])->latest();
 
        if ($statusFilter && in_array($statusFilter, ['Open', 'In Progress', 'Resolved'], true)) {
            $query->where('status', $statusFilter);
        }
 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
 
        $consultations = $query->paginate(15)->withQueryString();
        $activeConsultation = $consultation;
 
        return view('admin.consultations.index', compact('consultations', 'statusFilter', 'search', 'activeConsultation'));
    }
 
    /**
     * Start a new complaint/consultation thread (Citizen).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,docx,zip', 'max:5120'], // 5MB limit
        ]);
 
        $category = $validated['category'] ?? 'Complaint';
        $subject = $validated['subject'] ?? 'Direct Message';
 
        // Cooldown check (5 minutes for duplicate identical requests)
        $cooldownTime = now()->subMinutes(5);
        $isDuplicate = ConsultationRequest::where('user_id', auth()->id())
            ->where('category', $category)
            ->where('subject', $subject)
            ->where('message', $validated['message'])
            ->where('created_at', '>=', $cooldownTime)
            ->exists();
 
        if ($isDuplicate) {
            return response()->json([
                'errors' => [
                    'message' => ['Please wait 5 minutes before submitting an identical request.']
                ]
            ], 422);
        }
 
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('consultation-attachments', 'public');
        }
 
        $consultation = ConsultationRequest::create([
            'user_id' => auth()->id(),
            'category' => $category,
            'subject' => $subject,
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'status' => 'Open',
        ]);
 
        // Create initial message
        ComplaintMessage::create([
            'consultation_request_id' => $consultation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
            'attachment_path' => $attachmentPath,
        ]);
 
        return response()->json([
            'success' => true,
            'message' => 'Consultation thread started successfully.',
            'consultation_id' => $consultation->id,
            'tracking_id' => $consultation->tracking_id,
        ], 201);
    }
 
    /**
     * Fetch all messages in a consultation thread (JSON).
     */
    public function getMessages(ConsultationRequest $consultation): JsonResponse
    {
        if (auth()->user()->id !== $consultation->user_id && !auth()->user()->canAccessDashboard()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
 
        $messages = $consultation->messages()->with('sender')->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'body' => $msg->body,
                'attachment_path' => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
                'sender_name' => $msg->sender->name,
                'is_citizen' => $msg->sender->role === 'user',
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'formatted_time' => $msg->created_at->format('M d, Y, h:i A'),
            ];
        });
 
        return response()->json([
            'consultation' => [
                'id' => $consultation->id,
                'tracking_id' => $consultation->tracking_id,
                'subject' => $consultation->subject,
                'category' => $consultation->category,
                'status' => $consultation->status,
            ],
            'messages' => $messages,
        ]);
    }
 
    /**
     * Send a new message inside a thread.
     */
    public function sendMessage(Request $request, ConsultationRequest $consultation): JsonResponse
    {
        if (auth()->user()->id !== $consultation->user_id && !auth()->user()->canAccessDashboard()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
 
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,docx,zip', 'max:5120'], // 5MB limit
        ]);
 
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('consultation-attachments', 'public');
        }
 
        $message = ComplaintMessage::create([
            'consultation_request_id' => $consultation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['body'],
            'attachment_path' => $attachmentPath,
        ]);
 
        // Automatically transition status from 'Open' to 'In Progress' if an official responds
        if (auth()->user()->canAccessDashboard() && $consultation->status === 'Open') {
            $consultation->update(['status' => 'In Progress']);
        }
 
        // Broadcast Event
        broadcast(new MessageSent($message))->toOthers();
 
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                'sender_name' => $message->sender->name,
                'is_citizen' => $message->sender->role === 'user',
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'formatted_time' => $message->created_at->format('M d, Y, h:i A'),
            ],
            'consultation_status' => $consultation->status
        ]);
    }
 
    /**
     * Update the thread status (SK Official).
     */
    public function updateStatus(Request $request, ConsultationRequest $consultation): RedirectResponse|JsonResponse
    {
        if (!auth()->user()->canAccessDashboard()) {
            abort(403);
        }
 
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Open,In Progress,Resolved'],
        ]);
 
        $consultation->update([
            'status' => $validated['status']
        ]);
 
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $consultation->status
            ]);
        }
 
        return redirect()->back()->with('success', 'Consultation status updated to ' . $validated['status']);
    }
 
    /**
     * Fetch all complaint threads for the logged-in user as JSON.
     */
    public function getThreadsJson(Request $request): JsonResponse
    {
        $threads = ConsultationRequest::where('user_id', auth()->id())
            ->latest()
            ->get();
 
        return response()->json([
            'threads' => $threads
        ]);
    }

    /**
     * Get list of requests for the citizen to follow up on.
     */
    public function citizenRequests(Request $request): JsonResponse
    {
        $email = auth()->user()->email;

        $health = HealthRequest::where('email', $email)->get()->map(function ($item) {
            return [
                'id' => 'health_' . $item->id,
                'ref' => 'HEA-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'type' => 'Health Consultation',
                'title' => 'Health Consultation appointment',
                'status' => $item->status,
            ];
        });

        $medicine = MedicineRequest::where('email', $email)->get()->map(function ($item) {
            return [
                'id' => 'medicine_' . $item->id,
                'ref' => 'MED-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'type' => 'Medicine Delivery',
                'title' => 'Medicine request',
                'status' => $item->status,
            ];
        });

        $silid = SilidKarununganRequest::where('email', $email)->get()->map(function ($item) {
            return [
                'id' => 'silid_' . $item->id,
                'ref' => 'SIL-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'type' => 'Study Room Booking',
                'title' => 'Silid Karunungan booking',
                'status' => $item->status,
            ];
        });

        $sports = SportsRegistration::where('email', $email)->get()->map(function ($item) {
            return [
                'id' => 'sports_' . $item->id,
                'ref' => 'SPO-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'type' => 'Sports League',
                'title' => $item->sport . ' Registration',
                'status' => $item->status,
            ];
        });

        $custom = CustomRequest::with('initiative')->where('email', $email)->get()->map(function ($item) {
            return [
                'id' => 'custom_' . $item->id,
                'ref' => 'REQ-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'type' => $item->initiative ? $item->initiative->title : 'Custom Request',
                'title' => $item->initiative ? $item->initiative->title : 'Form Request',
                'status' => $item->status,
            ];
        });

        $requests = collect()
            ->concat($health)
            ->concat($medicine)
            ->concat($silid)
            ->concat($sports)
            ->concat($custom);

        return response()->json([
            'requests' => $requests
        ]);
    }
}

