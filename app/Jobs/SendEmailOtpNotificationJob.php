<?php

namespace App\Jobs;

use App\Notifications\EmailOtpNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendEmailOtpNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $email,
        public string $otp,
        public string $section,
        public string $userName,
        public string $locale,
    ) {
        $this->afterCommit();
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        Notification::route('mail', $this->email)
            ->notify(new EmailOtpNotification(
                $this->otp,
                $this->section,
                $this->userName,
                $this->locale,
            ));
    }
}
