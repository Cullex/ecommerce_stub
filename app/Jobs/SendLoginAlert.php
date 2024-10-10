<?php

namespace App\Jobs;

use App\Mail\LoginAlertMail;
use App\Mail\registeredNameMail;
use App\Mail\ResetTokenMail;
use App\Mail\WelcomeNameEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLoginAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $registeredName;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $registeredName
     */
    public function __construct($email , $registeredName)
    {
        $this->email = $email;
        $this->registeredName = $registeredName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new LoginAlertMail($this->registeredName));
        ////change welcome
    }

    public function tags()
    {
        return ['notification'];
    }
}
