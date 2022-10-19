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

            <div class="col-md-3 d-print-none" style="padding-right:0px;">
                <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filter By {{ ucfirst($filter) }}
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item {{ $filter == 'confirmed' ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/confirmed">Confirmed</a>
                            <a class="dropdown-item {{ $filter == 'declined' ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/declined">Declined</a>

                            @if ($event->end->schedule_end->isPast())
                                <a class="dropdown-item {{ !in_array($filter, ['confirmed', 'declined']) ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/all">All</a>
                            @else
                                <a class="dropdown-item {{ $filter == 'pending' ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/pending">Pending</a>
                                <a class="dropdown-item {{ !in_array($filter, ['confirmed', 'declined', 'pending']) ? 'active' : ''}}" href="{{ route('organizer.invitations.index', [$event->code]) }}/all">All</a>
                            @endif
                        </div>
                    </div>

                    @php
                        $filterable = $event->end->schedule_end->isPast()
                                    ? ['confirmed', 'declined', 'all', '']
                                    : ['confirmed', 'declined', 'all','pending', ''];
                    @endphp

                    @if(in_array($filter, $filterable))
                        <div class="btn-group" role="group">
                            <a href="{{ route('organizer.invitations.download', [$event->code, $filter]) }}" class="btn btn-secondary">Download</a>
                            @if (count($participants))
                                <a href="{{ route('organizer.invitations.print', [$event->code, $filter]) }}" target="_blank" class="btn btn-primary">Print</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="row">
            <div class="d-print-block d-none">
            <strong class="text-secondary">{{ ucfirst($filter) }}</strong>
            <p>
                Event Date: {{ date('M d Y') }}
                <br>
                Print Date: {{ date('M d Y') }}
            </p>
            </div>

            <div class="col-md-{{ $event->end->schedule_end->isPast() ? '12' : '6'}} col-sm-12">
                <table id="table" class="table table-bordered">
                    <thead class="none">
                        <th style="display:none">created_at</th> <!-- just for ordering -->
                        <th class="text-center">Response</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Organization</th>
                    </thead>
                    <tbody>
                        @forelse ($participants as $participant)
                            <tr>
                                <td style="display:none">{{ $participant['created_at'] }}</td> <!-- just for ordering -->
                                <td class="text-center">{{ $participant['response'] }}</td>
                                <td>{{ $participant['email'] }}</td>
                                <td>{{ $participant['name'] }}</td>
                                <td>{{ $participant['organization'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No guest invited yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <br>
                <br>
            </div>

            @if(!$event->end->schedule_end->isPast())
                <div class="col-md-6" style="padding-right:0px">
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

        /* #table_filter > label > input {
            height: 53px;
        }

        .tagify {
            height: 53px;
        } */

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
            event_is_past: '{{ false }}',
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