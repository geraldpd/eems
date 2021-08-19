@extends('layouts.organizer')

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="container">
        @foreach ($evaluations as $evaluation)
            <div class="jumbotron">
                <h1 class="display-4">{{ ucwords($evaluation->name) }}</h1>

                <p class="lead">{{ $evaluation->description }}</p>

                <hr class="my-4">

                {{ count($evaluation->questions_array) ? count($evaluation->questions_array).' Items' : 'No questions set' }}

                <a class="btn btn-link float-right" href="{{ route('organizer.evaluations.edit', [$evaluation->id]) }}">update</a>
            </div>
        @endforeach
    </div>

@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script type="text/javascript">
    </script>
@endpush