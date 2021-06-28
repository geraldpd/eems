<?php

namespace App\Mail;

use App\Jobs\SendEventInvitation;
use App\Models\Event;
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

    public $recipient;

    public $url;

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

        $this->sender = $sender;
        $this->recipient = $recipient;

        $this->url = route('organizer.events.show', [$event->code]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->sender)
            ->markdown('emails.events.invitation');
    }
}
