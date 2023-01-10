<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Attendee</title>

        {{--
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" type="text/css" href="{{ asset('scripts/plugins/DataTables/datatables.min.css') }}"/>
        --}}
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <link rel="icon" href="{{ asset('assets/EDUVENT.png') }}">
        @stack('styles')
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
                background-color: #ff6600;
                border-color: #ff6600;
                font-size: 16px;
                padding: 0;
                height: 50px;
                width: 50px;
                line-height: 50px;
                text-align: center;
                border-color: transparent;
                color: white;
                border: 1px solid #e5e5e5;
                border-radius: 0 !important;
            }
        </style>

    </head>

    <body class="body-wrapper">


        <div id="app">

            <!--========================================
            =            Navigation Section            =
            =========================================-->
            <nav class="navbar main-nav border-less fixed-top navbar-expand-lg p-0">
                <div class="container-fluid p-0">
                <!-- logo -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('events.index') }}">Events
                            <span>/</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.index') }}#news">News
                            <span>/</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.index') }}#about">About
                            <span>/</span>
                            </a>
                        </li>

                        @if(Auth::check())
                            @switch(Auth::user()->roles()->first()->name)
                                @case('attendee')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('attendee.events.index') }}">My Events<span>/</span></a>
                                    </li>
                                @break
                            @endswitch
                        @endif

                    </ul>

                    @guest
                        @if (Route::has('login'))
                            <li class="ticket">
                                <a href="{{ route('login') }}">
                                    <span style="border-left: none">
                                        {{ __('Login') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="ticket">
                                <a href="{{ route('register') }}">
                                    <span>{{ __('Register') }}</span>
                                </a>
                            </li>
                        @endif
                    @else
                        <a href="{{ route(Auth::user()->roles()->first()->name.'.profile.index') }}" class="ticket">
                            <span style="border-left: none">{{ Auth::user()->fullname }}</span>
                        </a>
                    @endguest

                </div>
                </div>
            </nav>

            @yield('content')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ asset('scripts/plugins/DataTables/datatables.min.js') }}"></script>
        @stack('scripts')
    </body>
</html>
