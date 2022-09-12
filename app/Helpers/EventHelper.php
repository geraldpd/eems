<?php

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        $start = Carbon::parse($event->schedule_start);
        $end = Carbon::parse($event->schedule_end);

        switch (true) {
            case $start->isFuture():
                return 'PENDING';
                break;

            case $start->isPast() && $end->isFuture():
                return 'ONGOING';
                break;

            case $start->isPast() && $end->isPast():
                return 'CONCLUDED';
                break;
        }
    }
}

if (! function_exists('eventHelperHasEvaluation')) {
    function eventHelperHasEvaluation($event)
    {
        return $event->evaluation_id && $event->evaluation_name && $event->evaluation_description && $event->evaluation_questions && $event->evaluation_html_form;
    }
}

if (! function_exists('eventHelperTemporaryDocumentHolder')) {
    function eventHelperTemporaryDocumentHolder()
    {
        $organizer = Auth::user();
        $temporary_document_path = "storage/users/organizers/$organizer->id/temp_docs";

        if (file_exists($temporary_document_path)) {
            foreach (glob($temporary_document_path."/*") as $filename) {
                unlink($filename);
            }
            return $temporary_document_path;
        }

        File::makeDirectory($temporary_document_path);
        return $temporary_document_path;
    }
}

if (! function_exists('eventHelperGetUploadedDocuments')) {
    function eventHelperGetUploadedDocuments($event)
    {
        $event_document_path = "storage/events/$event->id/documents";

        $documents = File::allFiles($event_document_path);

        return collect($documents)
        ->sortBy(function ($file) {
            return $file->getCTime();
        })
        ->mapWithKeys(function ($file) {
            return [$file->getBaseName() => [
                'public' => $file->getRealPath(),
                'asset' => asset($file->getPathName())
            ]];
        })
        ->all();
    }
}

if (! function_exists('eventScheduleStatus')) {
    function eventScheduleStatus($event_schedule)
    {
        $status = '';

        switch (true) {
            case $event_schedule->schedule_start->isPast():

                if($event_schedule->schedule_end->isPast()) {
                    $status = 'CONCLUDED';
                } else {
                    $status = 'ONGOING';
                }
            break;

            default: //!
                $status = 'PENDING';
                if($event_schedule->schedule_start->diffInDays(Carbon::now()) == 0) { //less than 24hrs

                    $hours_to_start = $event_schedule->schedule_start->diffInMinutes(Carbon::now(), true) / 60;

                    ///$status = $hours_to_start;

                    if($hours_to_start <= 3) {// if event starts in less than or equal to 3 hours
                        $status = 'SOON';
                    }

                }
            break;
        }

        return $status;
    }
}