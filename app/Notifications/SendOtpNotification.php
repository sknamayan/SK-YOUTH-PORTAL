<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('SK Namayan - Email Verification Code')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your 6-digit email verification code is:")
            ->line("**{$this->otp}**")
            ->line('This code is valid for 10 minutes. Do not share this code with anyone.')
            ->line('If you did not request this code, please ignore this email.');
    }
}
