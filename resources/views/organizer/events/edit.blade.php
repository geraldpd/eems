@extends('layouts.organizer')

@section('content')
  <div class="container">

    <div class="float-right">
        <a href="{{ route('organizer.events.show', [$event->id]) }}" class="btn btn-link">See preview</a>
    </div>

    <form method="POST" action="{{ route('organizer.events.update', [$event->id]) }}">
      @method('PUT')
      @csrf

      <input type="hidden" name="date" value="{{ $event->schedule_end->format('M d, Y')  }}">

      <h1>
        {{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}
      </h1>

      <div class="form-group">
        <label for="name">Name of this event</label>
        <input type="text" name="name" value="{{ $event->name }}" class="form-control" placeholder="Give this event a name!" autofocus>

        @if ($errors->has('text'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('name') }}</strong>
        </small>
        @endif
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="category_id">Category</label>
          <select name="category_id" id="category_id" class="form-control">
            <option value=""> Select Category </option>
              @foreach ($categories as $category)
              <option {{ $event->category_id == $category->id ? 'selected' : '' }} value="{{ $category->id }}"> {{ $category->name }} </option>
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
              <option {{ $event->type == $type ? 'selected' : '' }} value="{{ $type }}"> {{ $type }} </option>
              @endforeach
          </select>

          @if ($errors->has('type'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('type') }}</strong>
          </small>
          @endif
        </div>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ $event->description }}</textarea>

        @if ($errors->has('description'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('description') }}</strong>
        </small>
        @endif
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <select name="location" id="location" class="form-control">
          <option value=""> Select Location </option>
          <option {{ $event->location == 'venue' ? 'selected' : '' }} value="venue"> Venue </option>
          <option {{ $event->location == 'online' ? 'selected' : '' }} value="online"> Online </option>
        </select>

        @if ($errors->has('location'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('location') }}</strong>
        </small>
        @endif
      </div>

      <div class="form-group">
        <label for="documents">Documents</label>
        <input type="text" {{ $event->documents }}class="form-control" placeholder="documents">

        @if ($errors->has('documents'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('documents') }}</strong>
        </small>
        @endif
      </div>

      <div class="float-right">
        <a href="{{ route('organizer.events.index') }}" class="btn btn-link">Cancel</a>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>

  </div>
@endsection

@push('scripts')
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush