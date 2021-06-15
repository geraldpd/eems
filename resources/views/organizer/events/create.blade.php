@extends('layouts.organizer')

@section('content')
  <div class="container">

    @if ($date)
      <h1>{{ $date->format('M d, Y') }}</h1>
    @endif

    <form method="POST" action="{{ route('organizer.events.store') }}">
      @csrf

      <input type="hidden" name="date" value="{{ $date }}">

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="name">Start of Event</label>
          <input type="time" id="schedule_start" name="schedule_start" value="08:00" class="form-control" required>
        </div>

        <div class="col-md-6 form-group">
          <label for="name">End of Event</label>
          <input type="time" id="schedule_end" name="schedule_end" value="17:00" class="form-control" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="category_id">Category</label>
          <select name="category_id" id="category_id" class="form-control">
            <option value=""> Select Category </option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}"> {{ $category->name }} </option>
            @endforeach
          </select>

          @if ($errors->has('category_id'))
            <small class="help-block text-danger">
              <strong>{{ $errors->first('category_id') }}</strong>
            </small>
          @endif
        </div>

        <div class="col-md-6 form-group">
          <label for="type">Type</label>
          <select name="type" id="type" class="form-control">
            <option value=""> Select Event Type </option>
            @foreach (config('eems.event_types') as $type)
              <option value="{{ $type }}"> {{ $type }} </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" class="form-control" placeholder="Give this event a name!">
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <select name="location" id="location" class="form-control">
          <option value=""> Select Location </option>
          <option value="venue"> Venue </option>
          <option value="online"> Online </option>
        </select>
      </div>

      <div class="form-group">
        <label for="documents">Documents</label>
        <input type="text" class="form-control" placeholder="documents">
      </div>

      <div style="form-group">
        <a href="{{ route('organizer.events.index') }}" class="float-righ btn btn-secondary">Cancel</a>
        <button type="submit" class="float-righ btn btn-primary">Submit</button>
      </div>

      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif

    </form>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush