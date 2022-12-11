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
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit sed do eiusm tempor incididunt ut labore</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <div class="schedule-tab">
                        <ul class="nav nav-pills text-center">
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
                        </ul>
                    </div>

                    <div class="schedule-contents bg-schedule">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active schedule-item" id="nov20">
                                <!-- Headings -->
                                <ul class="m-0 p-0">
                                    <li class="headings">
                                        <div class="subject" style="width:50%">Event</div>
                                        <div class="speaker">Organizer</div>
                                        <div class="venue">Venue</div>
                                    </li>

                                    @if($events->count())

                                        @foreach ($events as $event)
                                            <li class="schedule-details">
                                                <div class="block">
                                                    @auth
                                                        @if ($event->attendees->pluck('id')->contains(Auth::user()->id))
                                                            @switch(eventHelperGetDynamicStatus($event))
                                                                @case('PENDING')
                                                                @case('ONGOING')
                                                                <i class="float-right text-success fas fa-check-circle" title="you will attend this event"></i>
                                                                @break

                                                                @default {{-- CONCLUDED --}}
                                                                <i class="float-right text-success fas fa-check-circle" title="you have attended this event"></i>
                                                            @endswitch
                                                        @endif
                                                    @endauth
                                                    <!-- Subject -->
                                                    <div class="subject" style="width:50%">
                                                        <a href="{{ route('events.show', [$event->code]) }}" class="text-decoration-none text-secondary">
                                                            <h3>
                                                                {{ $event->name }}
                                                            </h3>
                                                        </a>
                                                    </div>
                                                    <!-- Speaker -->
                                                    <div class="speaker">
                                                        <span class="name">{{ $event->organizer->fullName }}</span>
                                                    </div>
                                                    <!-- Venue -->
                                                    <div class="venue">{{ $event->location }}</div>

                                                    <div>

                                                        @include('partials.event_schedules')
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                        <div class="d-flex justify-content-center">
                                            {!! $events->links() !!}
                                        </div>
                                    @else
                                        No event yet!
                                    @endif

                                </ul>
                            </div>

                            <div class="tab-pane fade schedule-item" id="nov21">
                                <!-- Headings -->
                                <ul class="m-0 p-0">
                                    <li class="headings">
                                        <div class="time">Time</div>
                                        <div class="speaker">Speaker</div>
                                        <div class="subject">Subject</div>
                                        <div class="venue">Venue</div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">9.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-one.jpg" alt="speaker-thumb-one">
                                                <span class="name">Samanta Doe</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Introduction to Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium A</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">10.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-two.jpg" alt="speaker-thumb-two">
                                                <span class="name">Zerad Pawel</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Principle of Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium B</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">12.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-three.jpg" alt="speaker-thumb-three">
                                                <span class="name">Henry Mong</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Wp Requirements</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium C</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">2.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-four.jpg" alt="speaker-thumb-four">
                                                <span class="name">Baily Leo</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Introduction to Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium D</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">3.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-five.jpg" alt="speaker-thumb-five">
                                                <span class="name">Lee Mun</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Useful tips for Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium E</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">3.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-six.jpg" alt="speaker-thumb-six">
                                                <span class="name">Lee Mun</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Useful tips for Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium E</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-pane fade schedule-item" id="nov22">
                                <!-- Headings -->
                                <ul class="m-0 p-0">
                                    <li class="headings">
                                        <div class="time">Time</div>
                                        <div class="speaker">Speaker</div>
                                        <div class="subject">Subject</div>
                                        <div class="venue">Venue</div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">9.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-one.jpg" alt="speaker-thumb-one">
                                                <span class="name">Samanta Doe</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Introduction to Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium A</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">10.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-two.jpg" alt="speaker-thumb-two">
                                                <span class="name">Zerad Pawel</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Principle of Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium B</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">12.00 AM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-three.jpg" alt="speaker-thumb-three">
                                                <span class="name">Henry Mong</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Wp Requirements</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium C</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">2.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-four.jpg" alt="speaker-thumb-four">
                                                <span class="name">Baily Leo</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Introduction to Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium D</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">3.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-five.jpg" alt="speaker-thumb-five">
                                                <span class="name">Lee Mun</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Useful tips for Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium E</div>
                                        </div>
                                    </li>
                                    <!-- Schedule Details -->
                                    <li class="schedule-details">
                                        <div class="block">
                                            <!-- time -->
                                            <div class="time">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="time">3.00 PM</span>
                                            </div>
                                            <!-- Speaker -->
                                            <div class="speaker">
                                                <img src="images/speakers/speaker-thumb-six.jpg" alt="speaker-thumb-six">
                                                <span class="name">Lee Mun</span>
                                            </div>
                                            <!-- Subject -->
                                            <div class="subject">Useful tips for Wp</div>
                                            <!-- Venue -->
                                            <div class="venue">Auditorium E</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!--====  End of Schedule  ====-->



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
                            @default {{-- CONCLUDED --}}
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
    </div>

    @endsection

    @push('styles')
    <style>
        .description-div {
            max-height: 350px;
            overflow: hidden;
        }

    </style>
    @endpush