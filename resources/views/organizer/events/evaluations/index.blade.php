@extends('layouts.organizer')

@section('content')
<div class="container">

    <ol class="breadcrumb d-print-none">
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

        @include('organizer.events.evaluations.partials.preview')

        @if(!$event->invitations->count())
            <div class="col-md-12">
                <p>Dont forget to <a href="{{ route('organizer.invitations.index', [$event->code]) }}"> invite attendees</a> </p>
            </div>
        @endif
    </div>

</div>
@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
<script>
    $(function() {

        const evaluation_is_released = {!! $event->evaluation_is_released !!};
        const open_evaluation = _ => _.addClass('btn-primary').removeClass('btn-secondary').html('<i class="fas fa-lock-open"></i> Open for Evaluation');
        const close_evaluation = _ => _.addClass('btn-secondary').removeClass('btn-primary').html('<i class="fas fa-lock"></i> Close for Evaluation');

        $('.evaluation_is_released')
        .mouseover(function() {
            evaluation_is_released ? close_evaluation($(this)) : open_evaluation($(this));
        })
        .mouseleave(function() {
            evaluation_is_released ? open_evaluation($(this)) : close_evaluation($(this));
        });

        $('.remove-evaluation-sheet').on('click', _ => {
            window.Swal.fire({
                title: `Remove Evaluation Sheet?`,
                text: 'You wont be able to get your attendees feedback when you remove this evaluation sheet, proceed anyway?',
                icon: 'question',
                confirmButtonText: 'Yes',
                confirmButtonColor: '#007bff',
                showCancelButton: true
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $('#remove-evaluation-sheet-form').submit()
                }
            });

        });

        $('.print-button').on('click', _ => {
            window.print()
        });
    })
</script>
@endpush