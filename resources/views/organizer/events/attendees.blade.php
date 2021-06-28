@extends('layouts.organizer')

@section('content')
    <div class="container">
        <div class="row float-right">
            @if(!$event->schedule_start->isPast())
            <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
            @endif
            <a href="{{ route('organizer.events.index') }}"" class="btn btn-link">Events</a>
            <a href="{{ route('organizer.events.show', [$event->code]) }}" class="btn btn-link">Preview</a>
        </div>
    </div>

    <br>
    <br>

    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <h1>{{ $event->name }}</h1>

        <form method="POST" action="{{ route('organizer.invitations.store', [$event->code]) }}">
            @csrf

            <div class="input-group mb-3">
                <input type="text" name="invitees" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                <div class="input-group-append">
                  <button class="btn btn-primary send-invitation" disabled type="submit">SEND INVITATION</button>
                </div>
              </div>

              @if ($errors->has('invitees'))
                @php
                    dd($errors);
                @endphp
              @endif
        </form>

        <br>
        <br>

    </div>
@endsection

@push('styles')
    <style>
        .tagify--outside{
            border: 0;
        }

        .tagify--outside .tagify__input{
            order: -1;
            flex: 100%;
            transition: .1s;
        }

        .tagify--outside .tagify__input:hover{
            border-color:var(--tags-hover-border-color);
        }
        .tagify--outside.tagify--focus .tagify__input{
            transition:0s;
            border-color: var(--tags-focus-border-color);
        }

    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/@yaireo/tagify"></script>
    <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('scripts/organizer/events/attendees.js') }}"></script>

    <script>
        const config = {
            routes: {
                suggest_attendees : '{{ route('helpers.suggest_attendees') }}'
            }
        }
    </script>
@endpush