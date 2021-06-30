@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
            <h2 class="display-5">{{ $event->name }}</h2>
            <p class="lead">And an even wittier subheading.</p>
            </div>
            <div class="bg-white box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                <br>
                <div class="container">
                    {{ $event->description }}
                </div>
            </div>
        </div>
    </div>
@endsection