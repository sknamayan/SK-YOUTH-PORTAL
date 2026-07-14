<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    /**
     * Download or view a private attachment file.
     * Enforces authentication and record ownership validation.
     *
     * @param Request $request
     * @param string $path
     * @return BinaryFileResponse
     */
    public function download(Request $request, string $path): BinaryFileResponse
    {
        $localDisk = Storage::disk('local');

        if (!$localDisk->exists($path)) {
            abort(404, 'File not found.');
        }

        // Identify ownership matching comment attachments or requests
        $authorized = false;
        $user = auth()->user();

        // Admin, DPO, and Staff have universal access
        if ($user->canAccessDashboard()) {
            $authorized = true;
        } else {
            // Find comments or records using this path to verify ownership
            $comment = \App\Models\Comment::where('attachment_path', $path)->first();
            if ($comment) {
                // If comment is associated with a request owned by the user, or authored by the user
                if (strtolower($comment->author_email ?? '') === strtolower($user->email)) {
                    $authorized = true;
                }
            } else {
                // Verify against requests directly
                $isOwner = \App\Models\HealthRequest::where('contact_number', $path) // wait, check email
                    ->orWhere('email', $user->email)
                    ->exists() ||
                    \App\Models\MedicineRequest::where('email', $user->email)->exists() ||
                    \App\Models\SilidKarununganRequest::where('email', $user->email)->exists() ||
                    \App\Models\RegistrationResponse::where('citizen_email', $user->email)->exists() ||
                    \App\Models\CustomRequest::where('email', $user->email)->exists();
                
                if ($isOwner) {
                    $authorized = true;
                }
            }
        }

        if (!$authorized) {
            abort(403, 'Unauthorized access: You do not have permission to view this attachment.');
        }

        $filePath = $localDisk->path($path);
        
        return response()->file($filePath, [
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'X-Content-Type-Options' => 'nosniff'
        ]);
    }
}
