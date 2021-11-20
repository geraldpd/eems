@extends('layouts.attendee')

@section('content')

    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        @if($event->evaluation_is_released)
            <h2 class="text-secondary">{{ $event->evaluation_name }} Evaluation </h2>
            <p>{{ $event->evaluation_description }}</p>

            <div class="row justify-content-center">

                <div class="col-md-12">

                    <form action="{{ route('attendee.events.evaluate', [$event->code]) }}" method="POST">
                        @csrf

                        <ol class="evaluation-form d-none">
                            {!! $event->evaluation->html_form !!}
                        </ol>

                        <div class="form-group float-right">
                            <button class="btn btn-primary"> submit </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <h2>This form is no longer accepting responses and has been set to close <br> by the organizer.</h2>
            <p>Try again later once the event has concluded.</p>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.evaluation-form')
            .removeClass('d-none')
            .find('.edit-evaluation_type, .remove-evaluation_type')
            .remove();

            $('.align-middle').removeAttr('contenteditable')
        });
    </script>
@endpush
