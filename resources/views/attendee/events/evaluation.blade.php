@extends('layouts.attendee')

@section('content')

    <div class="container">
        <h2 class="text-secondary">{{ $event->name }} Evaluation </h2>
        <div class="row justify-content-center">

            <div class="col-md-12">

                <ol class="evaluation-form d-none">
                    {!! $event->evaluation->html_form !!}
                </ol>

                <div class="form-group float-right">
                    <button class="btn btn-primary"> submit </button>
                </div>
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
        });
    </script>
@endpush
