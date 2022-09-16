@extends('layouts.admin')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <h1>Event Type Management</h1>
            </div>
            <div class="col-md-6">
                <a href="{{ route('admin.types.create') }}" class="btn btn-primary float-right mt-2">Add Event Type</a>
            </div>
        </div>

        <table id="table" class="table table-striped table-bordered"  width="100%">
            <thead class="thead-dark">
                <tr>
                    <th style="display:none">created_at</th>
                    <th>Name</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($types as $type)
                    <tr id="{{ $type->id }}">
                        <td style="display:none">{{ $type->created_at }}</td>
                        <td>{{ $type->name }}</td>
                        <td class="text-center">{{ $type->is_active ? 'ACTIVE' : 'INACTIVE' }}</td>
                        <td>
                            <div class="float-right">
                                <a class="btn btn-link" href="{{ route('admin.events.index', ['type' => $type->id]) }}">Events</a>
                                <a class="btn btn-primary" href="{{ route('admin.types.edit', [$type->id]) }}">edit</a>
                                <span class="btn btn-secondary delete-row" data-id="{{ $type->id }}">delete</span>
                            </div>
                        </td>
                    </tr>
                @empty

                @endforelse
            </tbody>
        </table>
    </div>

    <form action="{{ route('admin.types.destroy', ['type_id']) }}" method="post" id="delete-resource-form">
        @csrf
        @method('delete')
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('scripts/admin/types/index.js') }}"></script>
@endpush
