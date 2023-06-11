<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\EventAttendee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventServices
{
    public function getFrontEndEvents(array $params)
    {
        $events = Event::query()
            ->with([
                'organizer',
                'attendees',
                'type',
                'category',
                'schedules',
                'evaluations'
            ])
            ->when($params['keyword'], function ($query) use ($params) {
                $keyword = "%$params[keyword]%";
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery
                        ->orWhere('name', 'like', $keyword)
                        ->orWhereRelation('type', 'name', 'like', $keyword)
                        ->orWhereRelation('category', 'name', 'like', $keyword);
                });
            })
            ->when($params['exclude_concluded'], function ($query) {
                $query->whereHas('end', fn ($end) => $end->whereDate('schedule_end', '>', Carbon::now()));
            })
            ->when(Auth::check(), function ($query) use ($params) {
                if ($params['has_attended']) {
                    $query
                        ->whereIn('events.id', Auth::user()->attendedEvents->pluck('id')->toArray());
                }
            })
            ->orderBy(
                EventSchedule::select('schedule_start')
                    ->whereColumn('event_id', 'events.id')
                    ->orderBy('schedule_start')
                    ->limit(1),
                $params['order']
            );

        return $events;
    }

    public function getFrontEndEventsPerDay(array $params)
    {

        $events = DB::table('event_schedules')
            ->join('events', 'event_schedules.event_id', '=', 'events.id')
            ->join('types', 'events.type_id', '=', 'types.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->join('users AS organizer', 'events.organizer_id', '=', 'organizer.id')
            ->when($params['keyword'], function ($query) use ($params) {
                $keyword = "%$params[keyword]%";
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery
                        ->orWhere('events.name', 'like', $keyword)
                        ->orWhere('types.name', 'like', $keyword)
                        ->orWhere('categories.name', 'like', $keyword);
                });
            })
            ->when($params['exclude_concluded'], function ($query) {
                $query->whereDate('schedule_end', '>=', Carbon::now());
            })
            ->when(Auth::check(), function ($query) use ($params) {
                if ($params['has_attended']) {
                    $query
                        ->whereIn('events.id', Auth::user()->attendedEvents->pluck('id')->toArray());
                }
            })
            ->select(
                'events.id as event_id',
                'events.code as event_code',
                'events.name as event_name',
                'events.location as event_location',
                'events.venue as event_venue',
                'events.description as event_description',

                'event_schedules.id as event_schedule_id',
                'event_schedules.schedule_start as schedule_start',
                'event_schedules.schedule_end as schedule_end',

                'organizer.firstname as organizer_firstname',
                'organizer.lastname as organizer_lastname',
                'organizer.is_approved as is_organizer_verified',

                'types.name as type_name',
                'categories.name as category_name',
            )
            ->orderBy('event_schedules.schedule_start');

        return $events;
    }

    public function getEventsInvited()
    {
        return Event::query()
            ->whereHas('invitations')
            ->whereRelation('invitations', 'email', '=', Auth::user()->email)
            ->whereRelation('end', 'schedule_end', '>', Carbon::now())
            ->whereDoesntHave('attendees', function ($query) {
                $query->where('users.id', '=', Auth::user()->id);
            });
    }

    public function downloadAttachment($document)
    {
        if (!$document) {
            abort(403, 'Document could not be found');
        }

        try {
            $path = decrypt($document);
        } catch (DecryptException $th) {
            abort(404, 'Document is invalid');
        }

        return Storage::disk('s3')->download($path);
    }

    public function getTemporaryDocs()
    {
        $temporary_document_path = "users/organizers/" . Auth::user()->id . "/temp_docs";
        $documents = Storage::disk('s3')->allFiles($temporary_document_path);

        return collect($documents)
            ->mapWithKeys(fn ($file) => [basename($file) => encrypt($file)])
            ->all();
    }

    public function getEventDocs(Event $event)
    {
        if (!$event->documents) {
            return [];
        }

        $event_document_path = "events/$event_id/documents";
        $documents = Storage::disk('s3')->allFiles($event_document_path);

        return collect($documents)
            ->mapWithKeys(fn ($file) => [basename($file) => encrypt($file)])
            ->all();
    }
}
