<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
}