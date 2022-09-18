<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventServices
{
    public function getFrontEndEvents(Array $params)
    {
        $events = Event::query()
            ->with([
                'attendees',
                'type',
                'category',
                'schedules',
                'evaluations'
            ])
            ->when($params['keyword'], function($query) use ($params) {
                $keyword = "%$params[keyword]%";
                $query->where(function($subQuery) use ($keyword){
                    $subQuery
                    ->orWhere('name', 'like', $keyword)
                    ->orWhereRelation('type', 'name', 'like', $keyword)
                    ->orWhereRelation('category', 'name', 'like', $keyword);
                });
            })
            ->when($params['exclude_concluded'], function($query) {
                $query->whereHas('end', fn($end) => $end->whereDate('schedule_end', '>=', Carbon::now()));
            })
            ->when(Auth::check(), function($query) use ($params) {
                if($params['has_attended']) {
                    $query
                    ->whereIn('events.id', Auth::user()->attendedEvents->pluck('id')->toArray());
                }
            })
            ->orderBy(
                EventSchedule::select('schedule_start')
                ->whereColumn('event_id', 'events.id')
                ->orderBy('schedule_start')
                ->limit(1)
            );

        return $events;
    }

    public function getEventsInvited()
    {
        return Event::query()
            ->whereHas('invitations')
            ->whereRelation('invitations', 'email', '=', Auth::user()->email)
            ->whereDoesntHave('attendees', function ($query) {
                $query->where('users.id', '=', Auth::user()->id);
            });
    }

    public function downloadEventAttachment($document)
    {
        if(! $document) {
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
        $temporary_document_path = "users/organizers/".Auth::user()->id."/temp_docs";
        $documents = Storage::disk('s3')->allFiles($temporary_document_path);

        return collect($documents)
        ->mapWithKeys(fn($file) => [basename($file) => encrypt($file)])
        ->all();
    }

    public function getEventDocs($event_id)
    {
        $event_document_path = "events/$event_id/documents";
        $documents = Storage::disk('s3')->allFiles($event_document_path);

        return collect($documents)
        ->mapWithKeys(fn($file) => [basename($file) => encrypt($file)])
        ->all();
    }
}