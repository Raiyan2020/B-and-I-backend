<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public User $user)
    {
        $this->afterCommit();
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        $this->user->notify(new VerifyEmailNotification());
    }
}
