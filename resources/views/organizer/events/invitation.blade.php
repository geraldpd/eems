@extends('layouts.organizer')

@section('content')
    <div class="container">

        <div class="row">
            @if(session()->has('message'))
                <div class="alert alert-info">
                    {{ session()->get('message') }}
                </div>
            @endif

            <br>

            <ol class="breadcrumb" style="width:100%">
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('organizer.events.show', [$event->code]) }}">{{ ucwords(strtolower($event->name)) }}</a></li>
                <li class="breadcrumb-item">Invitations</li>
            </ol>
        </div>

        <br>

        <div class="row">

            <div class="col-md-9" style="padding-left:0px;">
                <h1 class="float-left">{{ $event->name }}</h1>
            </div>

            @if ($event->schedule_start->isPast())

                    <div class="col-md-3" style="padding-right:0px;">
                        <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Filter By
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item {{ $filter == 'confirmed' ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/confirmed">Confirmed</a>
                                    <a class="dropdown-item {{ $filter == 'declined' ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/declined">Declined</a>
                                    <a class="dropdown-item {{ !in_array($filter, ['confirmed', 'declined']) ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/all">All</a>
                                </div>
                            </div>

                            @if(in_array($filter, ['confirmed', 'declined', 'all', '']))
                                <div class="btn-group" role="group">
                                    <a href="{{ route('organizer.invitations.download', [$event->code, $filter]) }}" class="btn btn-secondary">Download</a>
                                </div>
                            @endif
                        </div>
                    </div>
            @endif

        </div>

        <div class="row">
            <div class="col-md-{{ $event->schedule_start->isPast() ? '12' : '4'}} col-sm-12">
                <table id="table" class="table table-bordered">
                    <thead class="none">
                        <th style="display:none">created_at</th> <!-- just for ordering -->
                        <th>Invited</th>
                        <th class="text-center">Response</th>
                    </thead>
                    <tbody>
                        @forelse ($participants as $participant)
                            <tr>
                                <td style="display:none">{{ $participant['created_at'] }}</td> <!-- just for ordering -->
                                <td>{{ $participant['email'] }}</td>
                                <td class="text-center">{{ $participant['response'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td colspan="2" class="text-center">No guest invited yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <br>
                <br>
            </div>

            @if(!$event->schedule_start->isPast())
                <div class="col-md-8">
                    <form method="POST" action="{{ route('organizer.invitations.store', [$event->code]) }}">
                        @csrf

                        <div class="input-group mb-3">
                            <input type="text" name="invitees" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="email" aria-label="email" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-secondary send-invitation" disabled type="submit"> <i class="fas fa-paper-plane"></i> send </button>
                            </div>
                        </div>

                        @if ($errors->has('invitees'))
                            {{ $message }}
                        @endif
                    </form>

                    <br>
                    <br>

                </div>
            @endif

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .tagify--outside{
            border: 0;
            border: 1px solid #ced4da;
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

        .dataTables_paginate a{
            margin-right: 10px;
        }

        @media(max-width:400px){
            .col-md-8 {
                padding-left: 0px;;
                padding-right: 0px;;
            }
        }

        #table_wrapper > div:nth-child(2) > div {
            padding-right:0px;
            padding-left:0px;
        }

        #table_wrapper > div:nth-child(1) > div:nth-child(1) {
            display:none;
        }

        #table_wrapper > div:nth-child(1) > div:nth-child(2) {
            padding:0px;
        }

        #table_filter > label > input {
            margin-left:0px;
            width: 100%
        }

        #table_filter {
            text-align: left;
            width: 100%
        }
        #table_filter > label {
            width: 100%;
        }

        #table_wrapper > div:nth-child(1) > div.col-sm-12.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #table_wrapper > div:nth-child(3) > div.col-sm-12.col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
            padding-left:0px;
        }

        #table_wrapper > div:nth-child(3) > div.col-sm-12.col-md-7 {
            flex: 0 0 100%;
            max-width: 100%;
            padding-left:0px;
        }

        #table_paginate > ul {
            float:center;
        }

        #table_info {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        #table_filter > label > input {
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1.25rem;
            line-height: 1.5;
            border-radius: 0.3rem;
        }
    </style>

@endpush

@push('scripts')
    <script src="{{ asset('scripts/organizer/events/invitations.js') }}" defer></script>

    <script>
        const config = {
            event_is_past: '{{ $event->schedule_start->isPast() }}',
            routes: {
                suggest_attendees : '{{ route('helpers.suggest_attendees') }}'
            },
            event: {
                id: {{ $event->id }},
                blacklist: @json($event->invitations->pluck('email'))
            }
        }
    </script>
@endpush