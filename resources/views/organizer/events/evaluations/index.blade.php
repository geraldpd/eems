@extends('layouts.organizer')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('organizer.events.show', [$event->code]) }}">{{ ucwords(strtolower($event->name)) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Evaluations</li>
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            @switch(true)
                @case(!$event->evaluation_id)
                    <div class="col-md-6">
                        <h1 class="text-secondary">You dont seem to have set any evaluation sheet for this event.</h1>
                        <p>Why dont you give it an evaluation sheet so you can get what your attendees think about your event.</p>

                        <br>

                        <a href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}"  class="btn btn-secondary btn-block">
                            <h3><i class="fas fa-plus-square"></i> Create a new eveluation sheet</h3>
                        </a>

                        <a href="{{ route('organizer.evaluations.create') }}"  class="btn btn-secondary btn-block">
                            <h3><i class="fas fa-recycle"></i> Reuse existing eveluation sheet</h3>
                        </a>
                    </div>
                    @break
                @case($event->evaluation_id && !$event->schedule_start->isPast() && !$event->schedule_end->isPast())
                    <div class="col-md-6">
                        <h1 class="text-secondary">{{ $event->name }}</h1>
                    </div>

                    <div class="col-md-12">
                        <div class="jumbotron">
                            <h1 class="display-4">{{ ucwords($event->evaluation->name) }}</h1>

                            <p class="lead">{{ $event->evaluation->description }}</p>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    {{ $event->evaluation->questions_array ? count($event->evaluation->questions_array).' Entries' : 'No questions set' }}
                                </div>

                                <div class="col-md-6">
                                    <div class="float-right">
                                        <a class="btn btn-link" href="{{ route('organizer.evaluations.edit', [$event->evaluation->id]) }}">Modify evaluation Entries</a>
                                        <a class="btn btn-link" href="">Reuse another sheet</a>
                                        <a class="btn btn-link" href="">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    @break
                @case($event->evaluation_id && $event->schedule_start->isPast() && !$event->schedule_end->isPast())
                    <div class="col-md-6">
                        <h1 class="text-secondary">Event is ongoin</h1>
                        <p>Evalautions will sheet results will be shown here after the event concludes</p>
                        <h3>{{ $event->evaluation->name }}</h3>
                    </div>
                    @break
                @case($event->evaluation_id && $event->schedule_start->isPast() && $event->schedule_end->isPast())
                    <div class="col-md-6">
                        <h1 class="text-secondary">{{ $event->evaluation->name }}</h1>
                        show result
                    </div>
                    @break
            @endswitch
        </div>

    </div>
@endsection

@push('modals')
@endpush

@push('styles')

@endpush

@push('scripts')
@endpush