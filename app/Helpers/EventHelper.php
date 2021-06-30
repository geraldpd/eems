<?php

use App\Models\Event;

if (! function_exists('eventHelperGetCode')) {
    function eventHelperGetCode($id)
    {
        $code = '';

        do {
            $code = encrypt($id);
            $event = Event::where('code', $code)->first();
        } while ($event);

        return $code;
    }
}

if (! function_exists('eventHelperGetInvitationLink')) {
    function eventHelperGetInvitationLink($event, $email)
    {
        return route('event.invitation', [$event->code, encrypt($email)]);
    }
}