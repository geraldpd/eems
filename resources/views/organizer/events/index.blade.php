@extends('layouts.organizer')

@section('content')
    <div class="container">

        @foreach($events as $event)
            {{ $loop->iteration }}
            <br>
        @endforeach

    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {
            $('.btn-user').on('click', function() {
                $('table tbody').hide();

                $(`#${$(this).data('tbody')}`).show();
            });
        })
    </script>
@endpush
