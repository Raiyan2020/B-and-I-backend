<?php

namespace App\Jobs;

use App\Mail\EmailVerification;
use App\Mail\ResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user,$type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->type == 'verification'){
            $link = route('site.user.login',['token' => $this->user->token]);
            Mail::to($this->user->email)->send(new EmailVerification($link));
        }elseif ($this->type == 'resetPassword'){
            $link = route('site.reset-password',['token' => $this->user->token]);
            Mail::to($this->user->email)->send(new ResetPasswordMail($link));
        }

    }
}
