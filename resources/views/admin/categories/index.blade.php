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
                <h1>Category Management</h1>
            </div>
            <div class="col-md-6">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary float-right mt-2">Add Category</a>
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
                @forelse ($categories as $category)
                    <tr id="{{ $category->id }}">
                        <td style="display:none">{{ $category->created_at }}</td>
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ $category->is_active ? 'ACTIVE' : 'INACTIVE' }}</td>
                        <td>
                            <div class="float-right">
                                <a class="btn btn-link" href="{{ route('admin.events.index', ['category' => $category->id]) }}">Events</a>
                                <a class="btn btn-primary" href="{{ route('admin.categories.edit', [$category->id]) }}">edit</a>
                                <span class="btn btn-secondary delete-row" data-id="{{ $category->id }}">delete</span>
                            </div>
                        </td>
                    </tr>
                @empty

                @endforelse
            </tbody>
        </table>
    </div>

    <form action="{{ route('admin.categories.destroy', ['category_id']) }}" method="post" id="delete-resource-form">
        @csrf
        @method('delete')
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('scripts/admin/categories/index.js') }}"></script>
@endpush
