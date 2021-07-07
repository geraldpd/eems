@extends('layouts.admin')

@section('content')
    <div class="container">

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary float-right">Add Category</a>

        <br>
        <br>

        <table class="table table-bordered table-compact">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr id="{{ $category->id }}">
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ $category->is_active ? 'ACTIVE' : 'INACTIVE' }}</td>
                        <td>
                            <div>
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
    <script type="text/javascript">
        $(function($) {

            $('.destroy-button').on('click', function() {
                let that = this;

                axios.delete($(this).data('destroy_route'))
                    .then(function (response) {
                        // handle success
                        if(!response.data.result === 'success') {
                            alert('category cannot be deleted, an event is using this category');
                        }

                        alert('category deleted');
                        table.row( $(that).parents('tr') ).remove().draw(false);
                    })
                    .catch(function (error) {
                        // handle error
                        console.warn(error);
                    })
            });

            let table = $('.table').DataTable();
        })
    </script>
@endpush
