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

    <form method="POST" id="sendMail" action="{{ route('organizer.mails.send') }}">
      @csrf

      <div class="row">
        <div class="form-group col-md-12 col-lg-12 col-sm-12">
            <label for="email" class="mx-auto d-block">
              To
            </label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
            {!! hasError($errors, 'email') !!}
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-12 col-lg-12 col-sm-12">
            <label for="email" class="mx-auto d-block">
              Subject
            </label>
            <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}" required>
            {!! hasError($errors, 'subject') !!}
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6 col-lg-6 col-sm-12">
          <label for="cc" class="mx-auto d-block">
            CC
          </label>
            <div id="cc-tag" class="tagify--outside"></div>
            <input type="hidden" name="cc" id="cc">
            {!! hasError($errors, 'cc') !!}
        </div>

        <div class="form-group col-md-6 col-lg-6 col-sm-12">
            <label for="bcc" class="mx-auto d-block">
              BCC
            </label>
            <div id="bcc-tag" class="tagify--outside"></div>
            <input type="hidden" name="bcc" id="bcc">
            {!! hasError($errors, 'bcc') !!}
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-12 col-lg-12 col-sm-12">
            <label for="message" class="mx-auto d-block">
              Message
            </label>
            <textarea class="form-control" name="message" id="message" cols="30" rows="10">{{ old('message') }}</textarea>
            {!! hasError($errors, 'message') !!}
        </div>
      </div>

      <div class="editor"></div>

      <div class="float-right">
        <button type="submit" class="float-righ btn btn-primary">Send</button>
      </div>

    </form>

  </div>
@endsection

@push('styles')
<style>
  .ck-editor__editable_inline {
    min-height: 400px;
  }
</style>
@endpush

@push('scripts')
<script src="{{ asset('scripts/organizer/emails/index.js') }}" defer></script>
@endpush
