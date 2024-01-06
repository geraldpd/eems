@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Events 12 Month Overview</h6>
                <div class="dropdown no-arrow">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pie Chart -->
    <div class="col-xl-3 col-lg-3">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Category Sources</h6>
                <div class="dropdown no-arrow">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 text-center small">

                    @php
                        // Calculate the sum of all data values
                        $total = array_reduce($categoryPie, function ($sum, $item) {
                            return $sum + $item['data'];
                        }, 0);

                        // Calculate the percentage for each item and append to the label
                        $updatedData = array_map(function ($item) use ($total) {

                            if($item['data'] == 0 && $total == 0) {
                                $item_total = 0;
                            } else {
                                $item_total = $item['data'] / $total;
                            }

                            $percentage = $item_total * 100;
                            $item['label'] .= ' ' . number_format($percentage, 2) . '%';
                            return $item;
                        }, $categoryPie);

                    @endphp
                    @foreach ($updatedData as $category)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ $category['color'] }}"></i> {{ $category['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-3 col-lg-3">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Type Sources</h6>
                <div class="dropdown no-arrow">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="typeChart"></canvas>
                </div>
                <div class="mt-4 text-center small">

                    @php
                        // Calculate the sum of all data values
                        $total = array_reduce($typePie, function ($sum, $item) {
                            return $sum + $item['data'];
                        }, 0);

                        // Calculate the percentage for each item and append to the label
                        $updatedData = array_map(function ($item) use ($total) {

                            if($item['data'] == 0 && $total == 0) {
                                $item_total = 0;
                            } else {
                                $item_total = $item['data'] / $total;
                            }

                            $percentage = $item_total * 100;
                            $item['label'] .= ' ' . number_format($percentage, 2) . '%';
                            return $item;
                        }, $typePie);

                    @endphp
                    @foreach ($updatedData as $type)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ $type['color'] }}"></i> {{ $type['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3">
        <h2>Top Organizers </h2>

        @foreach ($topOrganizers as $organizer)
            @php
                if($organizer->hasRole('admin') || $organizer->hasRole('attendee')) {
                    continue;
                }
            @endphp
            <div class="col-lg-12 mb-1">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        <img src="{{ asset($organizer->profile_picture_path) }}"  style="height:50px; width:50px;" alt="organizer" class="rounded rounded-circle img-fluid">
                        <div class="text-white small float-right">
                            <strong>{{ $organizer->full_name }}</strong>
                            <br>
                            {{ $organizer->email }}
                            <br>
                            <strong>{{  $organizer->organized_events_count }} Events</strong>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="col-xl-3 col-lg-3">
        <h2>Top Attendees </h2>
        @foreach ($topAttendees as $attendee)
            @php
                if($attendee->hasRole('admin') || $attendee->hasRole('organizer')) {
                    continue;
                }
            @endphp
            <div class="col-lg-12 mb-1">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body">
                        <img src="{{ asset($attendee->profile_picture_path) }}"  style="height:50px; width:50px;" alt="attendee" class="rounded rounded-circle img-fluid">
                        <div class="text-white small float-right">
                            <strong>{{ $attendee->full_name }}</strong>
                            <br>
                            {{ $attendee->email }}
                            <br>
                            <strong>{{  $attendee->organized_events_count }} Events</strong>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')

<script>
    const config = {
        eventChart: @json($eventChart),
        categoryPie: @json($categoryPie),
        typePie: @json($typePie)
    }

    console.log(config.typePie)
</script>
<script src="{{ asset('assets/admin/vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/demo/chart-area-demo.js') }}"></script>
<script src="{{ asset('assets/admin/js/demo/chart-pie-demo.js') }}"></script>
@endpush
