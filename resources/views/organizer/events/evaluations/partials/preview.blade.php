@if($event->has_evaluation)
    @switch($event->dynamic_status)
        @case('CONCLUDED')
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
                    <h2 class="text-secondary">{{ $event->name }} has concluded</h2>

                    <div class="jumbotron">
                        <span>Evaluation sheet used:</span>
                        <h2 class="display-4">{{ ucwords($event->evaluation_name) }}</h2>

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

        @case('PENDING')
        @case('ONGOING')
            <div class="col-md-12">
                <div class="jumbotron">
                    <span>This event will be using:</span>
                    <h2 class="display-4">{{ ucwords($event->evaluation_name) }}</h2>

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

                                    <a class="btn btn-link" href="{{ route('organizer.evaluations.edit', [$event->evaluation_id, 'event' => $event->code]) }}">Modify evaluation entries</a>

                                    <a class="btn btn-link" href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}">Reuse another template</a>

                                    <button type="button" class="btn btn-link text-secondary remove-evaluation-sheet" href="">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @break
    @endswitch

    <div class="col-md-12 d-print-none">
        <form action="{{ route('organizer.events.evalautions.close-open', [$event]) }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $event->evaluation_is_released ? 'btn-primary' : 'btn-secondary' }} evaluation_is_released">
                @if($event->evaluation_is_released)
                <i class="fas fa-lock-open"></i> Open for Evaluation
                @else
                <i class="fas fa-lock"></i> Closed for Evaluation
                @endif
            </button>
        </form>
    </div>

@else
    <div class="col-md-7">

        @switch($event->dynamic_status)
        @case('CONCLUDED')
            <h2 class="text-secondary">No evaluation sheet has been applied to this event.</h2>
            <p>No results to show</p>
        @break

        @case('PENDING')
        @case('ONGOING')
        <h2 class="text-secondary">You don't seem to have set any evaluation sheet for this event.</h2>
        <p>Why dont you give it an evaluation sheet so you can get the attendees feedback.</p>

        <br>

        <a href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}"  class="btn btn-light">
            <i class="fas fa-plus-square"></i> Create new evaluation template
        </a>

        <a href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}"  class="btn btn-light">
            <i class="fas fa-recycle"></i> Reuse existing template
        </a>
        @break
        @endswitch

    </div>
@endif