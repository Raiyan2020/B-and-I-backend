<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        /** @var User $notifiable */
        $locale = method_exists($notifiable, 'preferredLocale') ? $notifiable->preferredLocale() : app()->getLocale();
        $originalLocale = app()->getLocale();
        app()->setLocale($locale);

        $message = (new MailMessage)
            ->subject(__('mail.verify_email.subject'))
            ->markdown('emails.verify-email', [
                'mailSection' => 'verify_email',
                'userName' => $notifiable->name ?: __('mail.verify_email.user_fallback'),
                'otp' => $notifiable->otp,
            ]);

        app()->setLocale($originalLocale);

        return $message;
    }
}
