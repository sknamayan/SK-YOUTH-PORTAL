<?php

namespace App\Services;

use App\Mail\RequestReceivedMail;
use App\Mail\StatusChangedMail;
use App\Mail\AccountCreatedMail;
use App\Mail\KkProfileSubmittedMail;
use App\Models\User;
use App\Models\KkProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailDispatchService
{
    /**
     * Queue a request-received email. Logs SMTP failures without interrupting the caller.
     */
    public function queueRequestReceived(object $requestModel): void
    {
        if (empty($requestModel->email)) {
            return;
        }

        try {
            Mail::to($requestModel->email)->queue(new RequestReceivedMail($requestModel));
        } catch (\Throwable $e) {
            Log::error('Failed to queue request received email.', [
                'email' => $requestModel->email,
                'model' => get_class($requestModel),
                'model_id' => $requestModel->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue a status-changed email. Logs SMTP failures without interrupting the caller.
     */
    public function queueStatusChanged(object $requestModel): void
    {
        if (empty($requestModel->email)) {
            return;
        }

        try {
            Mail::to($requestModel->email)->queue(new StatusChangedMail($requestModel));
        } catch (\Throwable $e) {
            Log::error('Failed to queue status changed email.', [
                'email' => $requestModel->email,
                'model' => get_class($requestModel),
                'model_id' => $requestModel->id ?? null,
                'status' => $requestModel->status ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue an account-created email. Logs SMTP failures without interrupting the caller.
     */
    public function queueAccountCreated(User $user): void
    {
        if (empty($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->queue(new AccountCreatedMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to queue account created email.', [
                'email' => $user->email,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue a KK Profile submission email. Logs SMTP failures without interrupting the caller.
     */
    public function queueKkProfileSubmitted(KkProfile $profile): void
    {
        if (empty($profile->email)) {
            return;
        }

        try {
            Mail::to($profile->email)->queue(new KkProfileSubmittedMail($profile));
        } catch (\Throwable $e) {
            Log::error('Failed to queue KK Profile submitted email.', [
                'email' => $profile->email,
                'profile_id' => $profile->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
