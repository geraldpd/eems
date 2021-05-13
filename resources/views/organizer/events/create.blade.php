[@extends('layouts.organizer')

@section('content')
  <div class="container">

    @if ($date)
    <h1>{{ $date->format('M d, Y') }}</h1>
    @endif

    <form method="POST" action="{{ route('organizer.events.store') }}">
      @csrf
      <div class="form-group">
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

      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" class="form-control" placeholder="Give this event a name!">
      </div>

      <div class="form-group">
        <label for="type">Type</label>
        <select name="type" id="type" class="form-control">
          <option value=""> Select Event Type </option>
          @foreach (config('eems.event_types') as $type)
          <option value="{{ $type }}"> {{ $type }} </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>
      </div>


      <div class="form-group">
        <label for="location">location</label>
        <textarea class="form-control" name="location" id="location" cols="30" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label for="documents">documents</label>
        <input type="text" class="form-control" placeholder="Enter email">
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
@endsection

@push('scripts')
@endpush