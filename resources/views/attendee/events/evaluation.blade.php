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
                            {!! $event->evaluation_html_form !!}
                        </ol>

                        @if(! eventHasRatingByAttendee($event))
                            <div class="col-md-12 text-secondary">
                                <label>Rate this Event</label>
                                <h3>
                                    <input type="hidden" name="star_rating" id="star_rating" value="0">
                                    <div class="stars float-left">
                                        <i class="fas fa-star star" data-value="5"></i>
                                        <i class="fas fa-star star" data-value="4"></i>
                                        <i class="fas fa-star star" data-value="3"></i>
                                        <i class="fas fa-star star" data-value="2"></i>
                                        <i class="fas fa-star star" data-value="1"></i>
                                    </div>
                                </h3>
                            </div>
                        @endif

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

@push('styles')
    <style>
        .star:hover,
        .star:hover ~ .star {
            color: #ffc107!important;
            cursor: pointer;
        }

        .stars {
            display: flex;
            flex-direction: row-reverse;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            $('.evaluation-form')
            .removeClass('d-none')
            .find('.edit-evaluation_type, .remove-evaluation_type')
            .remove();

            $('.align-middle').removeAttr('contenteditable')

            $('.star').on('click', function() {
                let rating = $(this).data('value');

                $('#star_rating').val(rating);
                $('.star').removeClass('text-warning');
                $(this).addClass('text-warning');
                $(this).siblings(':not(this)').each(function() {

                    if($(this).data('value') < rating) {
                        $(this).addClass('text-warning');
                    }
                })
            })
        });
    </script>
@endpush
