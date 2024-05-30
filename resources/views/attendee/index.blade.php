@extends('layouts.auth.attendee')

@section('content')
<div class="container">

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    {{-- <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }} as {{ Auth::user()->roles()->first()->name }}


                </div>
            </div>
        </div>
    </div> --}}

    <!--================================
    =            News Posts            =
    =================================-->

    <section class="news section">

        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form action="" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" name="keyword" placeholder="Search for events" aria-label="Search for events" value="{{ old('keyword') ? old('keyword') : request()->keyword}}">
                        <div class="input-group-append">
                            <button class="input-group-text" type="submit" style="background-color: #ff6600;color: white">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <br>

        <div class="row justify-content-center mt-5">

            @forelse($upcommingEvents as $event)
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="blog-post">
                        <div class="post-thumb">
                            <a href="{{ route('events.show', [$event->code]) }}">
                                <img src="{{ $event->banner ? asset($event->banner_path) : 'https://placehold.co/600x400?text=No+Event+Banner' }}" alt="post-image" class="img-fluid" style="max-height:222px; width:100%">
                            </a>
                        </div>
                        <div class="post-content">

                            <div class="post-title">
                                <h2><a href="{{ route('events.show', [$event->code]) }}">
                                    {{ __($event->name) }}
                                    @if($event->organizer->is_approved)
                                        <i class="fas fa-check-circle text-success" title="The event organizer is a verified user."></i>
                                    @endif
                                </a></h2>
                            </div>
                            <div class="post-meta">
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <i class="fa fa-microphone"></i>
                                        <a href="#">{{ $event->organizer->firstname }}</a>
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
                        </div>
                    </div>
                </div>
            @empty
                <h3>No Event Found</h3>
            @endforelse

        </div>
    </section>

    <div class="row justify-content-center mt-15">
        @if(count($upcommingEvents))
            {!! $upcommingEvents->links() !!}
        @endif
    </div>
    <!--====  End of News Posts  ====-->
</div>
@endsection

@push('styles')
        <!-- PLUGINS CSS STYLE -->
        <!-- Bootstrap -->
        <link href=" {{ asset('theme/source/plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href=" {{ asset('theme/source/plugins/font-awsome/css/font-awesome.min.css') }}" rel="stylesheet">
        <!-- Magnific Popup -->
        <link href=" {{ asset('theme/source/plugins/magnific-popup/magnific-popup.css') }}" rel="stylesheet">
        <!-- Slick Carousel -->
        <link href=" {{ asset('theme/source/plugins/slick/slick.css') }}" rel="stylesheet">
        <link href=" {{ asset('theme/source/plugins/slick/slick-theme.css') }}" rel="stylesheet">
        <!-- CUSTOM CSS -->
        <link href=" {{ asset('theme/source/css/style.css') }}" rel="stylesheet">

        <style>
            .page-item.active .page-link {
                background-color: #ff6600 !important;
                color: #fff !important;
                border-color: #ff6600 !important;
            }
        </style>
@endpush