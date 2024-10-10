<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class WelcomeNameEmail extends Mailable
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
        $message = "Dear {$this->registeredName} , your profile has been successfully registered";
        $mail = (new MailMessage)
            ->greeting('Good Day!')
            ->line($message)
            ->line('Thank you for using our application!');
        return  $this->subject('Reset Token')
            ->markdown('vendor.notifications.email' , $mail->data());

    }
}
