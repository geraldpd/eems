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

        <div class="row">
            @if(!$event->schedule_start->isPast())
                <div class="col-md-8">
                    <form method="POST" action="{{ route('organizer.invitations.store', [$event->code]) }}">
                        @csrf

                        <div class="input-group mb-3">
                            <input type="text" name="invitees" id="invitees" class="form-control form-control-lg tagify--outside" placeholder="email" aria-label="email" aria-describedby="basic-addon2">
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
            @endif

            <div class="col-md-{{ !$event->schedule_start->isPast() ? '4' : '12'}}">
                <table class="table table-bordered table-condensed table-hover">
                    <thead class="none">
                        <th style="display:none">created_at</th> <!-- just for ordering -->
                        <th>Invited</th>
                        <th class="text-center">Confirmed</th>
                    </thead>
                    <tbody>
                        @forelse ($event->invitations->sortBy('created_at') as $invitation)
                            <tr>
                                <td style="display:none">{{ $invitation->created_at }}</td> <!-- just for ordering -->
                                <td>{{ $invitation->guest?->email ?? $invitation->email }}</td>
                                <td class="text-center">{{ $invitation->guest?->has_confirmed }}</td>
                            </tr>
                        @empty
                            No one is invited yet
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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

        .dataTables_paginate a{
            margin-right: 10px;
        }

        #DataTables_Table_0_filter > label > input[type=search] {
            display: block;
            width: 100%;
            height: calc(1.6em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            margin-left: 0px;
        }

        #DataTables_Table_0_filter > label {
            width: 100%;
        }

        #DataTables_Table_0_wrapper > div:nth-child(1) > div:nth-child(1) {
            display: none;
        }

        #DataTables_Table_0_wrapper > div:nth-child(1) > div.col-sm-12.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #DataTables_Table_0_wrapper > div:nth-child(3) > div:nth-child(1) {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #DataTables_Table_0_wrapper > div:nth-child(3) > div.col-sm-12.col-md-7 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #DataTables_Table_0_paginate > ul {
            justify-content: center;
        }

        #DataTables_Table_0_info {
            text-align: center;
        }

    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/@yaireo/tagify"></script>
    <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('scripts/organizer/events/invitations.js') }}"></script>

    <script>
        const config = {
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