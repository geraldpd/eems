@extends('layouts.app')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                @if(Auth::check())

                    {{-- the attendee is invited to this event --}}
                    @if($event->invitations()->whereEmail(Auth::user()->email)->exists())

                        {{-- has accepted invitation --}}
                        @if($event->attendees()->whereAttendeeId(Auth::user()->id)->exists())

                            @if($event->schedule_start->ispast())

                                @if($event->schedule_end->ispast())
                                    <button class="btn btn-light float-right" disabled>You have attended this event</button>

                                    @if($event->has_evaluation && !in_array(Auth::user()->id, $event->evaluated_attendees) && in_array(Auth::user()->id, $event->attendees->pluck('id')->all()))
                                        <div class="float-right">
                                            <a class="btn btn-primary" href="{{ route('attendee.events.evaluation', [$event->code]) }}">Evaluate</a>
                                        </div>
                                    @endif

                                @else
                                    <button class="btn btn-light float-right" disabled>This event is on going</button>
                                @endif

                            @else
                                <div class="float-right">
                                    <button class="btn mr-2 btn-secondary" data-toggle="modal" data-target="#book_us-modal">Book for other attendees</button>
                                    <button class="btn mr-2 btn-light" disabled>You will be attending this event</button>
                                </div>
                            @endif

                        @else
                            <form action="{{ route('event.accept_booking_invitation', [$event->code]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary text-white float-right"> Accept Invitation </button>
                            </form>
                        @endif

                    @else

                        @if(! $event->schedule_start->ispast())
                            @if (Auth::user()->hasRole('attendee'))
                                <button class="btn btn-primary text-white float-right" id="book-event" data-code="{{ $event->code }}" data-toggle="modal" data-target="#book_me-modal"> Book this event </button>
                            @endif
                        @endif

                    @endif

                @else
                    <p class="float-right">Login or register to attend to this event</p>
                @endif
            </div>
        </div>

        <hr>

        <h1 class="display-3">{{ $event->name }}</h1>

        <div class="row">
            <div class="col-md-7">
                <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

                @if ($event->location == 'venue')
                    <p>Venue : <i> {{ $event->venue }} </i> </p>
                @else {{-- $event->location == 'online' --}}
                    @auth
                        @if($event->attendees()->whereAttendeeId(Auth::user()->id)->exists())
                            <p>Online : <a href="{{ $event->online }}" target="_blank"> {{ $event->online }} </a> </p>
                        @endif
                    @endauth
                @endif
            </div>

            @if(! $event->schedule_start->isPast())
                <div class="col-md-5">
                    <p>Share the Qrcode or copy the link to share this event to other users</p>
                    <div class="float-left">
                        <img  src="{{ asset($event->qrcode) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 150%;">
                    </div>

                    <div class="float-right">
                        <button class="btn btn-lg btn-light" id="invite_link_button">Copy link to clipboard</button>
                    </div>
                </div>
            @endif
        </div>

        <br>

        {!! $event->description !!}

    </div>

    @if(Auth::check())
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
                            <p>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal" data-toggle="modal" data-target="#book_us-modal">Book for other attendees</button>
                            <button type="submit" class="btn btn-primary">Book me</button>
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
                            <input type="text" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="email" aria-label="email" value="{{ $event->invitations()->exists(Auth::user()->email) ? '' : Auth::user()->email }}" aria-describedby="basic-addon2">
                        </div>

                        @if ($errors->has('email'))
                            {{ $message }}
                        @endif
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary send-invitation" form="mass-booking" disabled> <i class="fas fa-paper-plane"></i> send </button>
                </div>
                </div>
            </div>
        </div>
    @endif
    <input style="opacity: 0" type="text" id="invite_link" value="{{ route('events.show', $event->code).'?invite=true' }}">
@endsection

@push('styles')
    <style>
        .tagify--outside{
            border: 0;
            border: 1px solid #ced4da;
        }

        .tagify--outside .tagify__input{
            order: -1;
            flex: 100%;
            transition: .1s;
        }

        .tagify--outside .tagify__input:hover{
            border-color:var(--tags-hover-border-color);
        }
        .tagify--outside.tagify--focus .tagify__input{
            transition:0s;
            border-color: var(--tags-focus-border-color);
        }

        .dataTables_paginate a{
            margin-right: 10px;
        }

        @media(max-width:400px){
            .col-md-8 {
                padding-left: 0px;;
                padding-right: 0px;;
            }
        }

        #book_us-modal > div > div > div.modal-body > form > div > tags {
            display: inline-table;
        }
    </style>

@endpush

@push('scripts')
    <script src="{{ asset('scripts/front/events/invitations.js') }}" defer></script>

    <script>
        const config = {
            routes: {
                suggest_attendees : '{{ route('helpers.suggest_attendees') }}'
            },
            event: {
                id: {{ $event->id }},
                blacklist: @json($event->invitations->pluck('email'))
            },
            modals: {
                book_me : $('#book_me-modal'),
                book_us : $('#book_us-modal'),
            }
        }

        @if ($errors->has('email'))
            config.modals.book_us.modal('show')
        @endif

        $('#invite_link_button').on('click', function() {
            $(this).addClass('btn-success').removeClass('btn-light').html('<i class="fas fa-check"></i> Copied to clipboard');
            setTimeout(() => { $(this).addClass('btn-light').removeClass('btn-success').text('Copy link to clipboard')}, 1000);

            const invite_link = document.querySelector('#invite_link')
            invite_link.select();
            document.execCommand('copy')
        })
    </script>
@endpush