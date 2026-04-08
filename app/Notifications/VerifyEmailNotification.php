<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'api.v1.auth.verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );
    }

    public function toMail($notifiable): MailMessage
    {
        /** @var User $notifiable */
        $url = $this->verificationUrl($notifiable);
        $locale = method_exists($notifiable, 'preferredLocale') ? $notifiable->preferredLocale() : app()->getLocale();
        $originalLocale = app()->getLocale();
        app()->setLocale($locale);

        $message = (new MailMessage)
            ->subject(__('mail.verify_email.subject'))
            ->markdown('emails.verify-email', [
                'actionUrl' => $url,
                'user' => $notifiable,
            ]);

        app()->setLocale($originalLocale);

        return $message;
    }
}
