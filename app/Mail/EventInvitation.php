<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Event
     */
    public $event;

    public $sender;

    public $senderName;

    public $recipient;

    public $invitation_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $sender, $recipient)
    {
        $this->event = $event->load([
            'organizer',
            'category'
        ]);

        //$this->sender = $sender;
        $this->sender = env('MAIL_USERNAME', 'info.eduvent.ph@gmail.com');
        $this->senderName = User::whereEmail($sender)->first()->full_name;

        $this->recipient = $recipient; // email of the recipient

        $this->invitation_link = eventHelperSetInvitationLink($event, $recipient);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->sender, env('APP_NAME', 'EventHEI'))
            ->markdown('emails.events.invitation');
    }
}
