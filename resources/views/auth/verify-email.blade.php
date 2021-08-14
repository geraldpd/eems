
@php
    $layout = Auth::user()->roles()->first()->name;
@endphp

@extends('layouts.'.$layout)

@section('content')
    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4">{{ __('Verify Your Email Address') }}</h1>

            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <p class="lead">{{ __('Before proceeding, please check your email for a verification link.') }}</p>

            <hr class="my-4">

                {{ __('If you did not receive the email') }},
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                </form>
          </div>
    </div>
@endsection