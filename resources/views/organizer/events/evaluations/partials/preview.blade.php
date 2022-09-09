@switch(true)
    @case(!$event->has_evaluation) {{-- WHEN NO EVALUATION IS PROVIDED --}}
        <div class="col-md-6">
            @if($event->schedule_start->isPast()) {{-- WHEN EVENT HAS CONCLUDED --}}

                <h1 class="text-secondary">No evaluation sheet has been applied to this event.</h1>
                <p>No results to show</p>

            @else {{-- WHEN EVENT HAS CONCLUDED --}}

                <h1 class="text-secondary">You don't seem to have set any evaluation sheet for this event.</h1>
                <p>Why dont you give it an evaluation sheet so you can get what your attendees think about your event.</p>

                <br>

                <a href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}"  class="btn btn-secondary btn-block">
                    <h3><i class="fas fa-plus-square"></i> Create a new evaluation sheet</h3>
                </a>

                <a href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}"  class="btn btn-secondary btn-block">
                    <h3><i class="fas fa-recycle"></i> Reuse existing evaluation sheet</h3>
                </a>

                <br>

                @if(!$event->invitations->count())
                    <p>Dont forget to <a href="{{ route('organizer.invitations.index', [$event->code]) }}"> invite attendees</a> </p>
                @endif
            @endif

        </div>
    @break

    @case($event->has_evaluation && !$event->schedule_start->isPast() && !$event->schedule_end->isPast())  {{-- WHEN EVALUATION IS PROVIDED AND EVENT HAS NOT STARTED --}}
        <div class="col-md-6">
            <h1 class="text-secondary">{{ $event->name }}</h1>
        </div>

        <div class="col-md-12">
            <div class="jumbotron">
                <span>This event will be using:</span>
                <h1 class="display-4">{{ ucwords($event->evaluation_name) }}</h1>

                <p class="lead">{{ $event->evaluation_description }}</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        {{ $event->evaluation_questions_array ? count($event->evaluation_questions_array).' Entries' : 'No questions set' }}
                    </div>

                    <div class="col-md-6">
                        <div class="float-right">
                            <form id="remove-evaluation-sheet-form" action="{{ route('organizer.events.evaluations.destroy', [$event->code, $event->evaluation_id]) }}" method="post">
                                @csrf
                                @method('DELETE')

                                <a class="btn btn-link" href="{{ route('organizer.evaluations.edit', [$event->evaluation_id, 'event' => $event->code]) }}">Modify evaluation Entries</a>

                                <a class="btn btn-link" href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}">Reuse another sheet</a>

                                <button type="button" class="btn btn-link text-secondary remove-evaluation-sheet" href="">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$event->invitations->count())
            <p>Dont forget to <a href="{{ route('organizer.invitations.index', [$event->code]) }}"> invite attendees</a> </p>
            @endif
        </div>
    @break

    @case($event->has_evaluation && $event->schedule_start->isPast() && !$event->schedule_end->isPast())  {{-- WHEN EVALUATION IS PROVIDED AND EVENT IS ONGOING --}}
        <div class="col-md-12">

            <h1 class="text-secondary">{{ $event->name }} is in progress</h1>

            <div class="jumbotron">
                <span>Evaluation sheet used:</span>
                <h1 class="display-4">{{ ucwords($event->evaluation_name) }}</h1>

                <p class="lead">{{ $event->evaluation_description }}</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        {{ $event->evaluation_questions_array ? count($event->evaluation_questions_array).' Entries' : 'No questions set' }}
                    </div>

                    <div class="col-md-6">
                        <div class="float-right">
                            @if($event->attendees_count)
                            <p>Evaluation results will be shown here after the event concludes</p>
                            @else
                            <p> No one is invited in this event, Evaluation result could not be provided</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @break

    @case($event->has_evaluation && $event->schedule_start->isPast() && $event->schedule_end->isPast())  {{-- WHEN EVALUATION IS PROVIDED AND EVENT HAS CONCLUDED --}}
        @if($event->attendees_count)

            <div class="printable-evaluation col-md-12">
                <strong class="d-print-block d-none float-right">Print Date: {{ date('Y-m-d') }}</strong>
                <h4 class="text-secondary">{{ ucwords($event->name) }}</h4>
                <strong class="d-print-block d-none text-secondary">{{ ucwords($event->evaluation_name) }}</strong>
                <p class="d-print-block d-none">{{ $event->evaluation_description }}</p>
                @include('organizer.events.evaluations.partials.result')
            </div>

            @if ($event->evaluations->count())
            <div class="col-md-12 fixed-bottom pb-3 d-print-none">
                <div class="btn-group float-right" role="group">
                    <button class="print-button float-right btn btn-primary">Print</button>
                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Download as
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" href="{{ route('organizer.events.evaluations.download', [$event->code, 'as' => 'CSV']) }}">CSV</a>
                            <a class="dropdown-item" href="{{ route('organizer.events.evaluations.download', [$event->code, 'as' => 'JSON']) }}">JSON</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        @else
            <div class="col-md-12">
                <h1 class="text-secondary">{{ $event->name }} has concluded</h1>

                <div class="jumbotron">
                    <span>Evaluation sheet used:</span>
                    <h1 class="display-4">{{ ucwords($event->evaluation_name) }}</h1>

                    <p class="lead">{{ $event->evaluation_description }}</p>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            {{ $event->evaluation_questions_array ? count($event->evaluation_questions_array).' Entries' : 'No questions set' }}
                        </div>

                        <div class="col-md-6">
                            <p>No one has attended this event. No evaluation sheet result available.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @break
@endswitch