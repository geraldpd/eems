<?php

namespace App\Mail;

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

    public $url;

    public $is_preview;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $is_preview = false)
    {
        $this->event = $event->load([
            'organizer',
            'category'
        ]);

        $this->url = '#';

        $this->is_preview = $is_preview;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('view.name');
        return $this
            ->from('organizer@laravel.com')
            ->markdown('emails.events.invitation');
    }
}
