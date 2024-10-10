<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    private $registeredName;

    /**
     * Create a new message instance.
     *
     * @param $registeredName
     */
    public function __construct($registeredName)
    {
        $this->registeredName = $registeredName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $time = Carbon::now()->format('d/m/Y H:i:s');
        $message = "Dear {$this->registeredName} , there was a login on your account at ".$time. ' If 
        this is not you contact support to secure your account';
        $mail = (new MailMessage)
            ->greeting('Good Day!')
            ->line($message)
            ->line('Thank you for using our application!');
        return  $this->subject('Login Alert')
            ->markdown('vendor.notifications.email' , $mail->data());

    }
}
