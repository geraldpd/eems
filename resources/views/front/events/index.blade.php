@extends('layouts.app')

@section('content')



<!--==============================
    =            Schedule            =
    ===============================-->

    <section class="section schedule">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3>Event <span class="alternate">Schedule</span></h3>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit sed do eiusm tempor incididunt ut labore</p> --}}

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">


                    <div class="schedule-tab">

                        <div class="col-md-12">
                            <form action="" method="GET">
                                <div class="input-group mb-3">
                                    @csrf
                                    <input type="text" class="form-control form-control-lg" name="keyword" placeholder="Search for events" aria-label="Search for events" value="{{ old('keyword') ? old('keyword') : request()->keyword}}">
                                    <div class="input-group-append">
                                        <button class="input-group-text btn btn-main-md" type="submit">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- <ul class="nav nav-pills text-center">
                            <li class="nav-item">
                                <a class="nav-link active" href="#nov20" data-toggle="pill">
                                    Day-01
                                    <span>20 November 2017</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#nov21" data-toggle="pill">
                                    Day-02
                                    <span>21 November 2017</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#nov22" data-toggle="pill">
                                    Day-03
                                    <span>22 November 2017</span>
                                </a>
                            </li>
                        </ul> --}}
                    </div>

                    <div class="schedule-contents bg-schedule">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active schedule-item" id="nov20">


                                <!-- Headings -->
                                <ul class="m-0 p-0">
                                    <li class="headings">
                                        <div class="time">Schedule</div>
                                        <div class="speaker">Organizer</div>
                                        <div class="subject">Title</div>
                                        <div class="venue">Venue</div>
                                    </li>

                                    @if($events->count())
                                        @foreach ($events as $event)
                                            <a href="{{ route('events.show', $event->event_code) }}">
                                                <li class="schedule-details">
                                                    <div class="block">
                                                        <!-- time -->
                                                        <div class="time">
                                                            <i class="fa fa-clock-o"></i>
                                                            <span class="time">{{ eventScheduleDateFormatter($event->schedule_start) }}</span>
                                                        </div>
                                                        <!-- Speaker -->
                                                        <div class="speaker">
                                                            <span class="name">{{ $event->organizer_firstname.' '.$event->organizer_lastname }}</span>
                                                        </div>
                                                        <!-- Subject -->
                                                        <div class="subject">
                                                            @if ($event->is_organizer_verified)
                                                                <i class="fas fa-check-circle text-success"></i>
                                                            @endif
                                                            {{ $event->event_name }}
                                                        </div>
                                                        <!-- Venue -->
                                                        <div class="venue">{{ $event->event_location }}</div>
                                                    </div>
                                                </li>
                                            </a>
                                        @endforeach

                                         {!! $events->links() !!}
                                    @else

                                        <a href="#">
                                            <li class="schedule-details">
                                                <div class="block">
                                                   <h5 class="text-center"> No events yet! </h5>
                                                </div>
                                            </li>
                                        </a>

                                    @endif

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!--====  End of Schedule  ====-->


{{--
    <div class="container">
        <h1>BROWSE EVENTS</h1>
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form action="" method="GET">
                    <div class="input-group mb-3">
                        @csrf
                        <input type="text" class="form-control form-control-lg" name="keyword" placeholder="Search for events" aria-label="Search for events" value="{{ old('keyword') ? old('keyword') : request()->keyword}}">
                        <div class="input-group-append">
                            <button class="input-group-text" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            @if($events->count())

            @foreach ($events as $event)
            <div class="col-md-12">
                <a href="{{ route('events.show', [$event->code]) }}" class="text-decoration-none text-secondary">
                    <div class="card">
                        <div class="card-header">
                            @auth
                            @if ($event->attendees->pluck('id')->contains(Auth::user()->id))

                            @switch(eventHelperGetDynamicStatus($event))
                            @case('PENDING')
                            @case('ONGOING')
                            <i class="float-right text-success fas fa-check-circle" title="you will attend this event"></i>
                            @break
                            @default
                            <i class="float-right text-success fas fa-check-circle" title="you have attended this event"></i>
                            @endswitch

                            @endif
                            @endauth
                            <h2 class="text-dark">
                                {{ $event->name }}
                            </h2>
                        </div>

                        <div class="card-body">
                            @include('partials.event_schedules')

                            <div class="description-div">
                                {!! $event->description !!}
                            </div>

                            <a href="{{ route('events.show', [$event->code]) }}">...Read More</a>

                        </div>
                    </div>
                </a>
                <br>
            </div>
            @endforeach

            <div class="d-flex justify-content-center">
                {!! $events->links() !!}
            </div>
            @else
            No event yet!
            @endif
        </div>
    </div> --}}

    @endsection

    @push('styles')
    <style>
        .description-div {
            max-height: 350px;
            overflow: hidden;
        }

    </style>
    @endpush