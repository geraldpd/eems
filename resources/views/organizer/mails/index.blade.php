@extends('layouts.organizer')

@section('content')
  <div class="container">

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <h1>Emails</h1>
    <hr>
    <form method="POST" action="{{ route('organizer.mails.send') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">
        <div class="form-group col-md-12 col-lg-12 col-sm-12">
            <label for="to" class="mx-auto d-block">
              To
            </label>
            <input type="email" id="email" class="form-control" value="" required>
            {!! hasError($errors, 'name') !!}
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6 col-lg-6 col-sm-12">
            <label for="cc" class="mx-auto d-block">
              CC
            </label>
            <input type="text" id="cc" class="form-control" value="">
            {!! hasError($errors, 'cc') !!}
        </div>

        <div class="form-group col-md-6 col-lg-6 col-sm-12">
            <label for="bcc" class="mx-auto d-block">
              BCC
            </label>
            <input type="text" id="bcc" class="form-control" value="">
            {!! hasError($errors, 'bcc') !!}
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-12 col-lg-12 col-sm-12">
            <label for="message" class="mx-auto d-block">
              Message
            </label>
            <textarea class="form-control" name="message" id="" cols="30" rows="10"></textarea>
            {!! hasError($errors, 'message') !!}
        </div>
      </div>

      <div class="float-right">
        <button type="submit" class="float-righ btn btn-primary">Send</button>
      </div>

    </form>

  </div>
@endsection

@push('styles')

@endpush

@push('scripts')
@endpush
