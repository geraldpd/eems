@extends('layouts.organizer')

@section('content')

    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create') }}">Add Evaluation Sheet</a>
            </div>
        </div>

        <br>

        @forelse ($evaluations as $evaluation)
            <div class="jumbotron">
                <h1 class="display-4">{{ ucwords($evaluation->name) }}</h1>

                <p class="lead">{{ $evaluation->description }}</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        {{ $evaluation->questions_array ? count($evaluation->questions_array).' Entries' : 'No questions set' }}
                    </div>

                    <div class="col-md-6">
                    <form action="{{ route('organizer.evaluations.destroy', [$evaluation->id]) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="button" class="btn btn-link text-secondary float-right remove-evaluation_sheet" href="">remove</button>
                    </form>
                    <a class="btn btn-link float-right" href="{{ route('organizer.evaluations.edit', [$evaluation->id]) }}">update</a>
                    </div>
                </div>
            </div>
        @empty
            <h1>You dont have any Evaluation Sheets!</h1>
            <p>Why dont you make one, simply click on the Add Evaluation Sheet button.</p>
        @endforelse
    </div>

@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script type="text/javascript">
        $('.remove-evaluation_sheet').on('click', function(e) {

            window.Swal.fire({
                title: 'Delete this Evaluation Sheet?',
                text: 'Are you sure you want delete this evaluation sheet?',
                icon: 'question',
                confirmButtonText: 'Remove',
                confirmButtonColor: '#007bff',
                showCancelButton: true
            })
            .then((result) => {

                $(this).closest('form').submit();
            });


        })
    </script>
@endpush