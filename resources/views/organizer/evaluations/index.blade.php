@extends('layouts.organizer')

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create') }}">Create Evalaution Sheet</a>
            </div>
        </div>

        <br>

        @foreach ($evaluations as $evaluation)
            <div class="jumbotron">
                <h1 class="display-4">{{ ucwords($evaluation->name) }}</h1>

                <p class="lead">{{ $evaluation->description }}</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        {{ $evaluation->questions_array ? count($evaluation->questions_array).' Items' : 'No questions set' }}
                    </div>

                    <div class="col-md-6">
                    <form action="{{ route('organizer.evaluations.destroy', [$evaluation->id]) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-link text-secondary float-right" href="">remove</button>
                    </form>
                    <a class="btn btn-link float-right" href="{{ route('organizer.evaluations.edit', [$evaluation->id]) }}">update</a>
                    </div>
                </div>
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