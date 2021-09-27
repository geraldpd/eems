@extends('layouts.organizer')

@section('content')

    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        @if($event)
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.evaluations.index', [$event->code]) }}">{{ $event->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reuse Evaluation sheet</li>
            </ol>
        @endif

        <div class="row">
            <div class="col-md-9">
                @if ($event)
                    <h1 class="text-secondary"> Evaluation sheet for <a href="{{ route('organizer.events.evaluations.index', [$event->code]) }}">{{ $event->name }}</a> </h1>
                    <p>Select an evaluation sheet to reuse for your event</p>
                @endif
            </div>
            <div class="col-md-3">
                @if($event)
                    <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}">Add Evaluation Sheet</a>
                @else
                    <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create') }}">Add Evaluation Sheet</a>
                @endif
            </div>
        </div>

        <br>

        @forelse ($evaluations as $evaluation)
            <div class="jumbotron">
                <h1 class="display-4">{{ ucwords($evaluation->name) }}</h1>

                <p class="lead">{{ $evaluation->description }}</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        {{ $evaluation->questions_array ? count($evaluation->questions_array).' Entries' : 'No questions set' }}
                    </div>

                    <div class="col-md-6">
                        @if ($event)

                            @if($event->evaluation_id == $evaluation->id)
                                <button class="btn btn-light float-right" disabled>Selected Evaluation</button>
                            @else
                                <form action="{{ route('organizer.events.evaluations.update', [$event->code, $evaluation->id]) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-primary float-right use-evaluation_sheet" href="">Use This Evaluation Sheet</button>
                                </form>
                            @endif

                        @else

                            <a class="btn btn-link float-right" href="{{ route('organizer.evaluations.edit', [$evaluation->id]) }}">update</a>

                            <form action="{{ route('organizer.evaluations.destroy', [$evaluation->id]) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-link text-secondary float-right remove-evaluation_sheet" href="">remove</button>
                            </form>

                        @endif
                    </div>
                </div>
            </div>
        @empty
            <h1>You dont have any Evaluation Sheets!</h1>
            <p>Why dont you make one, simply click on the Add Evaluation Sheet button.</p>
        @endforelse
    </div>

@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script type="text/javascript">
        $('.remove-evaluation_sheet').on('click', function(e) {

            window.Swal.fire({
                title: 'Delete this Evaluation Sheet?',
                text: 'Are you sure you want delete this evaluation sheet?',
                icon: 'question',
                confirmButtonText: 'Remove',
                confirmButtonColor: '#007bff',
                showCancelButton: true
            })
            .then((result) => {
                if(result.isConfirmed) {
                    $(this).closest('form').submit();
                }
            });


        })
    </script>
@endpush