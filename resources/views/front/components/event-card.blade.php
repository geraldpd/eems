<div class="col-lg-4 col-md-6 col-sm-8">
    <div class="blog-post">
        <div class="post-thumb">
            <a href="{{ route('events.show', [$event->code]) }}">
                <img src="{{ $event->banner ? asset($event->banner_path) : 'https://placehold.co/600x400?text=No+Event+Banner' }}" alt="post-image" class="img-fluid" style="height:222px; width:100%;">
            </a>
        </div>
        <div class="post-content">

            <div class="post-title">
                <h2>
                    <a href="{{ route('events.show', [$event->code]) }}">
                        {{ __($event->name) }}
                        @if($event->organizer->is_approved)
                            <i class="fas fa-check-circle text-success" title="The event organizer is a verified user."></i>
                        @endif
                    </a>
                </h2>
            </div>
            <div class="post-meta">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <i class="fa fa-microphone"></i>
                        <a href="#">{{ $event->organizer->firstname }}</a>
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
        </div>
    </div>
</div>