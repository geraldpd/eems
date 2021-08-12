@extends('layouts.app')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        @if($event->invitations()->whereEmail(Auth::user()->email)->exists())
            @if(!$event->attendees()->whereAttendeeId(Auth::user()->id)->exists())
                <button class="btn btn-info text-white float-right"> Accept Invitation </button>
                <br>
                <br>
                <hr>
            @endif
        @endif

        <h1 class="display-5">{{ $event->name }}</h1>

        <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

        <br>

        {{ $event->description }}

    </div>
@endsection