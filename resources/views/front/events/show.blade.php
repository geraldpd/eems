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

                            @switch($event->dynamic_status)
                                @case('PENDING')
                                    <div class="float-right">
                                        <button class="btn btn-main-md mr-2 btn-secondary" data-toggle="modal" data-target="#book_us-modal">Book for other attendees</button>
                                        <button class="btn btn-main-md mr-2 btn-light" disabled>You will be attending this event</button>
                                    </div>
                                    @break
                                @case('ONGOING')
                                    <button class="btn btn-main-md btn-light float-right" disabled>This event is on going</button>
                                    @break
                                @default

                                    <button class="btn btn-main-md btn-light float-right" disabled>You have attended this event</button>

                                    @if($event->has_evaluation && !in_array(Auth::user()->id, $event->evaluated_attendees) && in_array(Auth::user()->id, $event->attendees->pluck('id')->all()))
                                        <div class="float-right">
                                            <a class="btn btn-main-md" href="{{ route('attendee.events.evaluation', [$event->code]) }}">Evaluate</a>
                                        </div>
                                    @endif

                            @endswitch

                        @else
                            <form action="{{ route('event.accept_booking_invitation', [$event->code]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-main-md text-white float-right"> Accept Invitation </button>
                            </form>
                        @endif

                    @else

                        @if($event->dynamic_status != 'CONCLUDED')
                            @if (Auth::user()->hasRole('attendee'))
                                <button class="btn btn-main-md text-white float-right" id="book-event" data-code="{{ $event->code }}" data-toggle="modal" data-target="#book_me-modal"> Book this event </button>
                            @endif
                        @endif

                    @endif

                @else
                    <p class="float-right">Login or register to attend to this event</p>
                @endif
            </div>
        </div>

            <div class="row mt-30">
                <div class="col-lg-12 col-md-12 mx-auto">
                    <div class="block">
                        <!-- Article -->
                        <article class="blog-post single">
                            <div class="post-thumb">
                                <img src="{{ asset('theme/source/images/news/single-post-short.jpg') }}" style="width:100%" alt="post-image" class="img-fluid">
                            </div>
                            <div class="post-content">
                                @if($event->schedules->count() > 1)
                                    <div class="date" style="width:12%">
                                        <h4>{{ $event->start->schedule_start->format('M') }}<span>{{ $event->start->schedule_start->format('d') }}</span></h4>
                                        &nbsp; to &nbsp;
                                        <h4>{{ $event->end->schedule_end->format('M') }}<span>{{ $event->end->schedule_end->format('d') }}</span></h4>
                                    </div>
                                @else
                                    <div class="date">
                                        <h4>20<span>May</span></h4>
                                    </div>
                                @endif

                                @if($event->dynamic_status != 'CONCLUDED')
                                <div class="float-right">
                                    <img class="rounded" src="{{ asset($event->qrcode_path) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="height: 90px;">
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
                                    {!! $event->description !!}
                                </div>


                                @auth
                                    @forelse ($event->uploaded_documents as $name => $path)
                                        @if ($loop->first)
                                        <div class="post-title">
                                            <h3>Uploaded Documents:</h3>
                                        </div>

                                        @endif
                                        <a href="{{ route('helpers.download-event-attachment', ['document' => $path]) }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                                            {{ $name }}
                                        </a>
                                        @if ($loop->last)
                                        <br>
                                            <sub>Uploaded documents will only be available for the events attendees.</sub>
                                            <br>
                                        @endif
                                    @empty
                                    @endforelse
                                @enauth

                            </div>
                        </article>


                        {{-- <!-- Comment Section -->
                        <div class="comments">
                            <h5>Comments (3)</h5>
                            <!-- Comment -->
                            <div class="media comment">
                                <img src="images/speakers/speaker-thumb-four.jpg" alt="image">
                                <div class="media-body">
                                    <h6>Jessica Brown</h6>
                                    <ul class="list-inline">
                                        <li class="list-inline-item"><span class="fa fa-calendar"></span>Mar 20, 2016</li>
                                        <li class="list-inline-item"><a href="#">Reply</a></li>
                                    </ul>
                                    <p>
                                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudant tota rem ape riamipsa eaque  quae nisi ut aliquip commodo consequat.
                                    </p>
                                    <!-- Nested Comment -->
                                    <div class="media comment">
                                        <img src="images/speakers/speaker-thumb-three.jpg" alt="image">
                                        <div class="media-body">
                                            <h6>Jonathan Doe</h6>
                                            <ul class="list-inline">
                                                <li class="list-inline-item"><span class="fa fa-calendar"></span>Mar 20, 2016</li>
                                            </ul>
                                            <p>
                                                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudant tota rem ape riamipsa eaque  quae nisi
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Comment -->
                            <div class="media comment">
                                <img src="images/speakers/speaker-thumb-two.jpg" alt="image">
                                <div class="media-body">
                                    <h6>Adam Smith</h6>
                                    <ul class="list-inline">
                                        <li class="list-inline-item"><span class="fa fa-calendar"></span>Mar 20, 2016</li>
                                        <li class="list-inline-item"><a href="#">Reply</a></li>
                                    </ul>
                                    <p>
                                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudant tota rem ape riamipsa eaque  quae nisi ut aliquip commodo consequat.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="comment-form">
                            <h5>Leave A Comment</h5>
                            <form action="#" class="row">
                                <div class="col-12">
                                    <textarea class="form-control main" name="comment" id="comment" rows="10" placeholder="Your Review"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control main" name="text" id="name" placeholder="Your Name">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control main" name="email" id="email" placeholder="Your Email">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-main-md btn-main-md" type="submit">Submit Now</button>
                                </div>
                            </form>
                        </div> --}}

                    </div>
                </div>
                {{-- <div class="col-lg-4 col-md-10 mx-auto">
                    <div class="sidebar">
                        <!-- Search Widget -->
                        <div class="widget search p-0">
                            <div class="input-group">
                                <input type="text" class="form-control main m-0" id="expire" placeholder="Search...">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                        <!-- Category Widget -->
                        <div class="widget category">
                            <!-- Widget Header -->
                            <h5 class="widget-header">Categories</h5>
                            <ul class="category-list m-0 p-0">
                                <li><a href="">Strategy Planning <span class="float-right">(6)</span></a></li>
                                <li><a href="">Corporate Identity <span class="float-right">(9)</span></a></li>
                                <li><a href="">Brand Creation<span class="float-right">(3)</span></a></li>
                                <li><a href="">Entertainment<span class="float-right">(5)</span></a></li>
                                <li><a href="">Conference<span class="float-right">(7)</span></a></li>
                            </ul>
                        </div>
                        <!-- Latest post -->
                        <div class="widget latest-post">
                            <h5 class="widget-header">Latest Post</h5>
                            <!-- Post -->
                            <div class="media">
                                <img src="images/news/post-thumb-sm-one.jpg" class="img-fluid" alt="post-thumb">
                                <div class="media-body">
                                    <h6><a href="">Nam hendrerit eros in ligula suscipit suscipit</a></h6>
                                    <p href="#"><span class="fa fa-calendar"></span>02 Feb, 2017</p>
                                </div>
                            </div>
                            <!-- Post -->
                            <div class="media">
                                <img src="images/news/post-thumb-sm-two.jpg" class="img-fluid" alt="post-thumb">
                                <div class="media-body">
                                    <h6><a href="">Nam hendrerit eros in ligula suscipit suscipit</a></h6>
                                    <p href="#"><span class="fa fa-calendar"></span>02 Feb, 2017</p>
                                </div>
                            </div>
                            <!-- Post -->
                            <div class="media">
                                <img src="images/news/post-thumb-sm-three.jpg" class="img-fluid" alt="post-thumb">
                                <div class="media-body">
                                    <h6><a href="">Nam hendrerit eros in ligula suscipit suscipit</a></h6>
                                    <p href="#"><span class="fa fa-calendar"></span>02 Feb, 2017</p>
                                </div>
                            </div>
                            <!-- Post -->
                            <div class="media">
                                <img src="images/news/post-thumb-sm-four.jpg" class="img-fluid" alt="post-thumb">
                                <div class="media-body">
                                    <h6><a href="">Nam hendrerit eros in ligula suscipit suscipit</a></h6>
                                    <p href="#"><span class="fa fa-calendar"></span>02 Feb, 2017</p>
                                </div>
                            </div>
                        </div>
                        <!-- Popular Tag Widget -->
                        <div class="widget tags">
                            <!-- Widget Header -->
                            <h5 class="widget-header">Popular Tags</h5>
                            <ul class="list-inline">
                                <li class="list-inline-item"><a href="#">Culture</a></li>
                                <li class="list-inline-item"><a href="#">Social</a></li>
                                <li class="list-inline-item"><a href="#">News</a></li>
                                <li class="list-inline-item"><a href="#">Events</a></li>
                                <li class="list-inline-item"><a href="#">Sports</a></li>
                                <li class="list-inline-item"><a href="#">Music</a></li>
                            </ul>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

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
                        @include('partials.event_schedules')
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-main-md btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-main-md btn-light" data-dismiss="modal" data-toggle="modal" data-target="#book_us-modal">Book for other attendees</button>
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
                        <input type="text" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="email" aria-label="email" value="{{ $event->invitations()->exists(Auth::user()->email) ? '' : Auth::user()->email }}" aria-describedby="basic-addon2">
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
@endif

    <input style="opacity: 0" type="text" id="invite_link" value="{{ route('events.show', $event->code).'?invite=true' }}">


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