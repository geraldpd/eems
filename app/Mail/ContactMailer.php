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
    public $uploads;

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
        $this->uploads = $data['uploads'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$from = 'info.eduvent.ph@gmail.com';
        $from = request()->user()->email;

        $mailable = $this->from($from, request()->user()->fullname)
        ->subject($this->subject)
        ->markdown('emails.contact');

        if($this->uploads) {
            foreach($this->uploads as $path) {
                $mailable->attach($path);
            }
        }

        return $mailable;
    }
}
