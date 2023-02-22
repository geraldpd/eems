<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $myCc;
    public $myBcc;
    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->myCc = $data['cc'] ?? [];
        $this->myBcc = $data['bcc'] ?? [];
        $this->message = $data['message'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from(request()->user()->email, request()->user()->fullname)
        ->subject($this->subject)
        ->markdown('emails.contact');
    }
}
