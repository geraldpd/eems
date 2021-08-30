<?php

use App\Models\Event;

if (! function_exists('eventHelperSetCode')) {
    function eventHelperSetCode($id)
    {
        $code = '';

        do {
            $code = encrypt($id);
            $event = Event::where('code', $code)->first();
        } while ($event);

        return $code;
    }
}

if (! function_exists('eventHelperSetInvitationLink')) {
    function eventHelperSetInvitationLink($event, $email)
    {
        return route('event.invitation', [$event->code, encrypt($email)]);
    }
}

if (! function_exists('resetNotifConfirmedAttendeeCount')) {
    function resetNotifConfirmedAttendeeCount($event)
    {
        $event->attendees()->whereIsConfirmed(1)->whereIsNotified(0)->update(['is_notified' => true]);
    }
}

if (! function_exists('eventHelperGetDynamicStatus')) {
    function eventHelperGetDynamicStatus($event)
    {
        switch(true) {
            case !$event->schedule_start->isPast() && !$event->schedule_end->isPast():
                 $dynamic_status = 'PENDING';
            break;
            case $event->schedule_start->isPast() && !$event->schedule_end->isPast():
                 $dynamic_status = 'ONGOING';
            break;
            default: // AKA $event->schedule_start->isPast() && $event->schedule_end->isPast():
                $dynamic_status = 'CONCLUDED';
            break;
        }

        return $dynamic_status;
    }
}

if (! function_exists('eventHelperHasEvaluation')) {
    function eventHelperHasEvaluation($event)
    {
        return $event->evaluation_id && $event->evaluation_name && $event->evaluation_description && $event->evaluation_questions;
    }
}