<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly string $mailSection,
        private readonly string $userName,
        private readonly string $mailLocale,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $originalLocale = app()->getLocale();
        app()->setLocale($this->mailLocale);

        $message = (new MailMessage)
            ->subject(__('mail.'.$this->mailSection.'.subject'))
            ->markdown('emails.verify-email', [
                'mailSection' => $this->mailSection,
                'userName' => $this->userName,
                'otp' => $this->otp,
            ]);

        app()->setLocale($originalLocale);

        return $message;
    }
}
