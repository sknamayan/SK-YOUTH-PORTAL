<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Services\RequestManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(
        private readonly RequestManagementService $requestManagement
    ) {}

    /**
     * Store a comment on any request type.
     */
    public function store(Request $request, string $type, int $id): RedirectResponse
    {
        $requestModel = $this->requestManagement->resolveModel($type, $id);
        $user = auth()->user();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx'],
        ]);

        $attachmentPath = null;
        $attachmentOriginal = null;
        $attachmentMime = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('comment-attachments/' . $type . '/' . $id, 'public');
            $attachmentOriginal = $file->getClientOriginalName();
            $attachmentMime = $file->getMimeType();
        }

        $isStaff = $user && $user->canAccessDashboard();

        Comment::create([
            'commentable_type' => get_class($requestModel),
            'commentable_id' => $requestModel->id,
            'user_id' => $user?->id,
            'author_name' => $isStaff ? $user->name : ($user?->name ?? trim(($requestModel->first_name ?? '') . ' ' . ($requestModel->last_name ?? ''))),
            'author_email' => $user?->email ?? $requestModel->email,
            'body' => $validated['body'],
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginal,
            'attachment_mime' => $attachmentMime,
            'is_staff' => $isStaff,
        ]);

        ActivityLog::record(
            'comment_added',
            $requestModel,
            ['author' => $user?->email ?? $requestModel->email, 'is_staff' => $isStaff]
        );

        return back()->with('success', 'Message posted successfully.');
    }

    /**
     * Download a comment attachment.
     */
    public function downloadAttachment(string $type, int $requestId, Comment $comment)
    {
        $requestModel = $this->requestManagement->resolveModel($type, $requestId);

        if ($comment->commentable_type !== get_class($requestModel) || (int) $comment->commentable_id !== (int) $requestModel->id) {
            abort(404);
        }

        if (!$comment->hasAttachment() || !Storage::disk('public')->exists($comment->attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $comment->attachment_path,
            $comment->attachment_original_name ?? 'attachment'
        );
    }
}
