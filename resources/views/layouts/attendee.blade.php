<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Attendee</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="icon" href="{{ asset('assets/EDUVENT.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')

    <style>
        .bg-primary {
            background-color:#ff6600 !important
        }
    </style>

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('attendee.events.index') }}"> My Events </a>
                        </li>

                        @auth
                        <a class="nav-link" href="{{ route('attendee.invitations.index') }}">
                            Invitations

                            @php
                                $invitations_count = 0;//$my_invitations;
                            @endphp
                            @if ($invitations_count)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $invitations_count }}
                                </span>
                            @endif

                        </a>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                    @guest
                            @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->fullname }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                @switch(Auth::user()->roles()->first()->name)
                                @case('admin')
                                <a class="dropdown-item" href="{{ route('admin.profile.index') }}"> Profile</a>

                                @break
                                @case('organizer')
                                <a class="dropdown-item" href="{{ route('organizer.profile.index') }}"> Profile</a>

                                @break
                                @default {{-- attendee,admin,organizer --}}
                                <a class="dropdown-item" href="{{ route('attendee.events.index') }}"> My Events</a>
                                <a class="dropdown-item" href="{{ route('attendee.profile.index') }}"> Profile</a>
                                @endswitch


                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <br>
        @yield('content')
        <br>
        <br>
        <br>
    </div>


    @stack('modals')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('scripts/plugins/DataTables/datatables.min.js') }}"></script>

@stack('scripts')
</body>
</html>