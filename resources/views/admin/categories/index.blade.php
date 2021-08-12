@extends('layouts.admin')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary float-right">Add Category</a>

        <br>
        <br>

        <table id="table" class="table">
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
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Events</a>
                                {!! tableActions($category, 'admin.categories') !!}
                            </div>
                        </td>
                    </tr>
                @empty

                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('scripts/admin/categories/index.js') }}"></script>
   {{-- {!! tableScript('categories') !!} --}}
@endpush