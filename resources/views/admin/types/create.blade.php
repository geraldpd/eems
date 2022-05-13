@extends('layouts.admin')

@section('content')
  <div class="container">

    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.types.index') }}">Event Types</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</a></li>
    </ol>

    <h1>Create Event Type</h1>

    <form method="POST" action="{{ route('admin.types.store') }}">
      @csrf

        <div class="form-group">
            {!! hasError($errors, 'name') !!}
            <input type="text" name="name" id="name" class="form-control">
        </div>

        <div class="custom-control custom-checkbox mr-sm-2">
            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" checked>
            <label class="custom-control-label" for="is_active">Active</label>
        </div>

        <div class="float-right">
            <a href="{{ route('admin.categories.index') }}" class="float-righ btn btn-link">Cancel</a>
            <button type="submit" class="float-righ btn btn-primary">Submit</button>
        </div>

    </form>

  </div>
@endsection
{{--
@push('scripts')
  <script src="{{ asset('plugins/moment.js') }}"></script>
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush --}}