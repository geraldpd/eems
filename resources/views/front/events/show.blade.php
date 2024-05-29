@extends('layouts.app')

@section('content')


    <!--================================
        =            Page Title            =
        =================================-->

    <section class="page-title bg-title overlay-dark">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="title">
                        <h3>{{ $event->name }}</h3>
                    </div>
                    <ol class="breadcrumb justify-content-center p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                        <li class="breadcrumb-item active">{{ $event->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!--====  End of Page Title  ====-->


    <!--================================
        =            News Posts            =
        =================================-->

    <section class="news section">
        <div class="container">

            @if (session()->has('message'))
                <div class="alert alert-info">
                    {{ session()->get('message') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    @if (Auth::check())

                        {{-- the attendee is invited to this event --}}
                        @if ($event->invitations()->whereEmail(Auth::user()->email)->exists())

                            {{-- has accepted/confirmed invitation --}}
                            @if (eventBookingIsConfirmed($event))
                                @switch($event->dynamic_status)
                                    @case('PENDING')
                                        <div class="block">
                                            <div class="container row" style="justify-content: space-between">
                                                @if ($event->booked_participants < $event->max_participants)
                                                    <div clss="col-md-6">
                                                        <button class="btn btn-main-md mr-2 btn-secondaryt" data-toggle="modal" data-target="#book_us-modal">Invite for other attendees</button>
                                                    </div>
                                                @endif

                                                <div class="col-md-6">
                                                    @if (eventBookingIsApproved($event))
                                                        <p class="h5">Your booking is <span class="alternate">Approved</span>! You will be attending this event</p>
                                                        {{-- <button class="btn btn-main-md mr-2 btn-light" disabled>You will be attending this event</button> --}}
                                                    @else
                                                        <p class="h5 text-right">Your Booking is <span class="alternate">Pending Approval</span></p>
                                                        {{-- <button class="btn btn-main-md mr-2 btn-light" disabled>Your Booking is Pending Approval</button> --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    @case('ONGOING')
                                        @if (eventBookingIsApproved($event))
                                            <h5>This event is <span class="alternate">on going!</span></h5>
                                        @else
                                            <h5>Sorry, Your Booking was <span class="alternate">not approved</span> by the organizer.</h5>
                                        @endif
                                    @break

                                    @default
                                        {{-- CONCLUDED --}}
                                        @if (eventBookingIsApproved($event))
                                            <button class="btn btn-main-md btn-light float-right" disabled>You have attended this event</button>

                                            @if ($event->has_evaluation && !in_array(Auth::user()->id, $event->evaluated_attendees) && in_array(Auth::user()->id, $event->attendees->pluck('id')->all()))
                                                <div class="float-right">
                                                    <a class="btn btn-main-md" href="{{ route('attendee.events.evaluation', [$event->code]) }}">Evaluate</a>
                                                </div>
                                            @endif

                                            @if (!eventHasRatingByAttendee($event))
                                                <div class="float-right">
                                                    <button class="btn btn-main-md rate-button">Rate</button>
                                                </div>
                                            @endif
                                        @else
                                            <h5>Sorry, Your Booking was <span class="alternate">not approved</span> by the organizer.</h5>
                                        @endif
                                @endswitch
                            @else
                                @if ($event->booked_participants < $event->max_participants)
                                    @switch($event->dynamic_status)
                                        @case('PENDING')
                                            @if (eventBookingIsDisapproved($event))
                                                <h5>Sorry, Your Booking was <span class="alternate">not approved</span> by the organizer.</h5>
                                            @else
                                                <form action="{{ route('event.accept_booking_invitation', [$event->code]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-main-md text-white float-right"> Accept Invitation </button>
                                                </form>
                                            @endif
                                        @break

                                        @case('ONGOING')
                                            {{-- Do nothing --}}
                                        @break

                                        @default
                                            {{-- Do nothing --}}
                                        @break
                                    @endswitch
                                @else
                                    <button class="btn btn-main-md btn-light float-right" disabled>This event has reached its maximum participants</button>
                                @endif
                            @endif

                        @else
                            {{-- the event has not yet concluded and not invited--}}
                            @if ($event->dynamic_status != 'CONCLUDED')
                                @if (Auth::user()->hasRole('attendee'))
                                    <button class="btn btn-main-md text-white float-right" id="book-event" data-code="{{ $event->code }}" data-toggle="modal" data-target="#book_me-modal"> Book this event </button>
                                @endif
                            @endif

                        @endif
                    @else
                        <p class="float-right">Login or register to check to this event</p>
                    @endif
                </div>
            </div>

            <div class="row mt-30">
                <div class="col-lg-12 col-md-12 mx-auto">
                    <div class="block">
                        <!-- Article -->
                        <article class="blog-post single">
                            <div class="post-thumb">
                                <img src="{{ $event->banner ? asset($event->banner_path) : 'https://placehold.co/600x200?text=No+Event+Banner' }}" style="width:100%;" alt="event banner" class="img-fluid">
                            </div>
                            <div class="post-content">
                                {{-- @if ($event->schedules->count() > 1)
                                    <div class="date" style="width:12%">
                                        <h4>{{ $event->start->schedule_start->format('M') }}<span>{{ $event->start->schedule_start->format('d') }}</span></h4>
                                        &nbsp; to &nbsp;
                                        <h4>{{ $event->end->schedule_end->format('M') }}<span>{{ $event->end->schedule_end->format('d') }}</span></h4>
                                    </div>
                                @else
                                    <div class="date">
                                        <h4>20<span>May</span></h4>
                                    </div>
                                @endif --}}

                                @if ($event->dynamic_status != 'CONCLUDED')
                                    <div class="float-right">
                                        <img class="rounded" src="{{ asset($event->qrcode_path) }}" alt="{{ route('events.show', $event->code) . '?invite=true' }}" style="height: 90px;">
                                    </div>
                                @endif

                                <div class="post-title">

                                    <h3>{{ $event->name }}</h3>

                                </div>
                                <div class="post-meta">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <i class="fa fa-microphone"></i>
                                            <a href="#">{{ $event->organizer->fullname }}</a>
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="fa fa-heart-o"></i>
                                            <a href="#">{{ $event->type->name }}</a>
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="fa fa-square-o"></i>
                                            <a href="#">{{ $event->category->name }}</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="post-details">
                                    @include('partials.event_schedules')

                                    @if ($event->location == 'online')
                                        @auth
                                            @if (eventBookingIsConfirmed($event) && eventBookingIsApproved($event))
                                                <a href="{{ $event->venue }}">{{ $event->venue }}</a>
                                            @else
                                                <span>Link will be available for attendees only</span>
                                            @endif
                                        @endauth
                                    @else
                                        <address>{{ $event->venue }}</address>
                                    @endif

                                    {!! $event->description !!}
                                </div>

                                @auth
                                    @if (eventBookingIsConfirmed($event) && eventBookingIsApproved($event))
                                        @forelse ($event->uploaded_documents as $name => $path)
                                            @if ($loop->first)
                                                <div class="post-title">
                                                    <h3>Uploaded Documents:</h3>
                                                </div>
                                            @endif
                                            <a href="{{ route('helpers.download-file', ['document' => $path]) }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                                                {{ $name }}
                                            </a>
                                            @if ($loop->last)
                                                <br>
                                                <sub>Uploaded documents will only be available for the events attendees.</sub>
                                                <br>
                                            @endif
                                        @empty
                                        @endforelse
                                    @endif
                                @endauth

                            </div>
                        </article>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <input style="opacity: 0" type="text" id="invite_link" value="{{ route('events.show', $event->code) . '?invite=true' }}">
@endsection

@push('modals')

    @if (Auth::check())
        <div class="modal fade" id="book_me-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('event.book', [$event->code]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="email[]" value="{{ Auth::user()->email }}">

                        <div class="modal-header">
                            <h5 class="modal-title" id="book_me-modal-label">Booking</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <h3>Book yourself to <span class="event-name">{{ $event->name }}?</span></h3>
                            @include('partials.event_schedules')
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-main-md btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-main-md btn-light" data-dismiss="modal" data-toggle="modal" data-target="#book_us-modal">Invite for other attendees</button>
                            <button type="submit" class="btn btn-main-md">Book me</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="book_us-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="book-us-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="book-us-label">Book This Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="{{ route('event.book', [$event->code]) }}" method="POST" id="mass-booking">
                            @csrf

                            <div class="emails-div"></div>

                            <div class="input-group mb-3">
                                <input type="text" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="email" aria-label="email"
                                    value="{{ $event->invitations()->exists(Auth::user()->email) ? '' : Auth::user()->email }}" aria-describedby="basic-addon2">
                            </div>

                            @if ($errors->has('email'))
                                {{ $message }}
                            @endif
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-main-md btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-main-md btn-secondary send-invitation" form="mass-booking" disabled> <i class="fas fa-paper-plane"></i> send </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rate_modal" tabindex="-1" aria-labelledby="rate_modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body row justify-content-center">

                        <div class="justify-content-center">
                            <h3>Rate this Event</h3>
                        </div>

                        <br>

                        <h1>
                            <div class="stars">
                                <i class="fas fa-star star" data-value="5"></i>
                                <i class="fas fa-star star" data-value="4"></i>
                                <i class="fas fa-star star" data-value="3"></i>
                                <i class="fas fa-star star" data-value="2"></i>
                                <i class="fas fa-star star" data-value="1"></i>
                            </div>
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    @endif

@endpush

@push('styles')
    <style>
        .tagify--outside {
            border: 0;
            border: 1px solid #ced4da;
        }

        .tagify--outside .tagify__input {
            order: -1;
            flex: 100%;
            transition: .1s;
        }

        .tagify--outside .tagify__input:hover {
            border-color: var(--tags-hover-border-color);
        }

        .tagify--outside.tagify--focus .tagify__input {
            transition: 0s;
            border-color: var(--tags-focus-border-color);
        }

        .dataTables_paginate a {
            margin-right: 10px;
        }

        @media(max-width:400px) {
            .col-md-8 {
                padding-left: 0px;
                ;
                padding-right: 0px;
                ;
            }
        }

        #book_us-modal>div>div>div.modal-body>form>div>tags {
            display: inline-table;
        }

        .star:hover,
        .star:hover~.star {
            color: #ffc107 !important;
            cursor: pointer;
        }

        .stars {
            display: flex;
            flex-direction: row-reverse;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('scripts/front/events/invitations.js') }}" defer></script>

    <script>
        const config = {
            routes: {
                suggest_attendees: '{{ route('helpers.suggest_attendees') }}'
            },
            event: {
                id: {{ $event->id }},
                blacklist: @json($event->invitations->pluck('email'))
            },
            modals: {
                book_me: $('#book_me-modal'),
                book_us: $('#book_us-modal'),
            }
        }

        @if ($errors->has('email'))
            config.modals.book_us.modal('show')
        @endif

        $('#invite_link_button').on('click', function() {
            $(this).addClass('btn-success').removeClass('btn-light').html('<i class="fas fa-check"></i> Copied to clipboard');
            setTimeout(() => {
                $(this).addClass('btn-light').removeClass('btn-success').text('Copy link to clipboard')
            }, 1000);

            const invite_link = document.querySelector('#invite_link')
            invite_link.select();
            document.execCommand('copy')
        });

        $('.rate-button').on('click', () => {
            $('#rate_modal').modal('show')
        });

        $('.star').on('click', function() {
            let star = $(this).data('value');

            axios.post("{{ route('attendee.events.rate', [$event->code]) }}", {
                    rating: star
                })
                .then(function(response) {
                    $('.rate-button').remove()
                    $('#rate_modal').modal('hide');

                    if (response.data.result == 'success') {
                        window.Swal.fire(
                            'Thank you for rating',
                            'success'
                        )
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        })
    </script>
@endpush
