<div class="upcoming-event-card card mb-3" style="height: 200px; width: 100%"
    data-search_name="{{ $event->name }}"
    data-search_organization="{{ $event->organizer->organization->name }}"
    data-search_type="{{ $event->type->name }}"
    data-search_category="{{ $event->category->name }}">

    <div class="row g-0">

        <div class="col-md-4">
            <a href="{{ route('events.show', [$event->code]) }}">
                <img src="{{ $event->banner ? asset($event->banner_path) : 'https://placehold.co/600x400?text=No+Event+Banner' }}" alt="post-image" class="img-fluid  rounded-start" style="height:200px;">
            </a>
        </div>

        <div class="col-md-8">
            <div class="card-body">
                <h3 class="card-title">
                    <a href="{{ route('events.show', [$event->code]) }}">
                        {{ __($event->name) }}
                    </a>
                    @if ($event->organizer->is_approved)
                        <i class="fas fa-check-circle text-success" title="Verified Organizer."></i>
                    @endif
                </h3>
                <div>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <i class="fa fa-microphone"></i>
                            <a href="#">{{ $event->organizer->organization->name }}</a>
                        </li>
                        <li class="list-inline-item">
                            <i class="fa fa-heart-o"></i>
                            <a href="#">{{ $event->type->name }}</a>
                        </li>
                        <li class="list-inline-item">
                            <i class="fa fa-square-o"></i>
                            <a href="#">{{ $event->category->name }}</a>
                        </li>
                    </ul>
                </div>
                <p class="card-text"><small class="text-body-secondary">Last Update {{ $event->updated_at->diffForHumans() }}</small></p>
            </div>
        </div>

    </div>

</div>

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            $('#search-event').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();

                $('.upcoming-event-card').each(function() {

                    var name = $(this).data('search_name').toLowerCase();
                    var organization = $(this).data('search_organization').toLowerCase();
                    var type = $(this).data('search_type').toLowerCase();
                    var category = $(this).data('search_category').toLowerCase();

                    if (name.indexOf(searchTerm) !== -1 ||
                        organization.indexOf(searchTerm) !== -1 ||
                        type.indexOf(searchTerm) !== -1 ||
                        category.indexOf(searchTerm) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endpush
