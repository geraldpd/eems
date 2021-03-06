<?php

use App\Models\Event;
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
                'public' => $file->getLinkTarget(),
                'asset' => asset($file->getPathName())
            ]];
        })
        ->all();
    }
}
