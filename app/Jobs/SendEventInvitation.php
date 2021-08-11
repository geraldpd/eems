<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Event;
use App\Mail\EventInvitation;
use Illuminate\Support\Facades\Mail;

class SendEventInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $event;

    public $sender;

    public $recipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event, $sender, $recipients)
    {
        $this->event = $event;

        $this->sender = $sender;

        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //? to run a job: php artisan queue:work
        foreach($this->recipients as $recipient) {
            Mail::to($recipient)->send(new EventInvitation($this->event, $this->sender, $recipient));
        }
    }
}
