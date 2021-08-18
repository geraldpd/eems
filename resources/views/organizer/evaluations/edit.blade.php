@extends('layouts.organizer')

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="container">
        <ol class="row questions-div"></ol>

        <hr>

        <div class="row jumbotron">
            <div class="col-md-8">
                <select class="form-control" name="evaluation_type" id="evaluation_type">
                    <option value=""> Select Item </option>
                    @foreach (config('eems.evaluation_types') as $evaluation_type => $attributes)
                        <option value="{{ $evaluation_type }}" data-attributes='@json($attributes)'> {{ ucwords($evaluation_type) }} </option>
                    @endforeach
                </select>

                <div class="row form_builder-div mt-2"></div>
            </div>

            <div class="col-md-4">
                <button type="button" id="add-evaluation_type" class="btn btn-light mb-2 btn-block">Add Item</button>
                <br>

                <form action="{{ route('organizer.evaluations.update', [$evaluation->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="html_form" id="html_form" required>
                    <input type="hidden" name="questions" required>
                    <button type="submit" id="save-evaluation_form" class="btn btn-primary mb-2 btn-block">Save Evaluation Form</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script type="text/javascript">
        const config = {
            evaluation_type: @json(config('eems.evaluation_types'))
        }
    </script>
    <script src="{{ asset('scripts/organizer/evaluations/edit.js') }}"></script>
@endpush