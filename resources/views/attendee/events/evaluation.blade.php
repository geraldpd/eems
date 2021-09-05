@extends('layouts.attendee')

@section('content')

    <div class="container">
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
