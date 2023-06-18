@extends('layouts.app')

@section('content')

<section class="banner bg-banner-one overlay">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<!-- Content Block -->
				<div class="block">
					<!-- Coundown Timer -->
					<div class="timer"></div>
					<h1>Eduvent</h1>
					<h5 class="text-white"><i>An Educational Events Portal for you</i></h5>
					{{-- <h2>Educational Event</h2>
					<h6>02-05 July 2017 California</h6> --}}
					<!-- Action Button -->
					{{-- <a href="contact.html" class="btn btn-white-md">get ticket now</a> --}}
				</div>
			</div>
		</div>
	</div>
</section>

<section class="news section">
	<div class="container">

		<div class="section-title">
			<h3>Upcoming <span class="alternate">Events</span></h3>
		</div>
		<div class="row">

			<div class="row justify-content-center mt-5">

				@forelse($events as $event)
					<div class="col-lg-4 col-md-6 col-sm-8">
						<div class="blog-post">
							<div class="post-thumb">
								<a href="{{ route('events.show', [$event->code]) }}">
									<img src="{{ asset($event->banner_path) }}" alt="post-image" class="img-fluid" style="max-height:222px; width:100%">
								</a>
							</div>
							<div class="post-content">

								<div class="post-title">
									<h2><a href="{{ route('events.show', [$event->code]) }}">
										{{ __($event->name) }}
										@if($event->organizer->is_approved)
											<i class="fas fa-check-circle text-success" title="The event organizer is a verified user."></i>
										@endif
									</a></h2>
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
				@empty
					<h3>No Event Found</h3>
				@endforelse

			</div>
		</div>
	</div>
</section>

<section class="section speakers bg-speaker overlay-lighter">
	<div class="container top-organizers">
		<div class="row">
			<div class="col-12">
				<div class="section-title white">
					<h3>Top <span class="alternate">Organizers</span></h3>
					<p>Discover the foremost event organizers renowned for their prolific expertise and unparalleled track record. With their unmatched activity and extensive experience, they are the pinnacle of event management excellence.</p>
				</div>
			</div>
		</div>
		<div class="row">
			@foreach ($topOrganizers as $organizer)
				@php
					if($organizer->hasRole('admin') || $organizer->hasRole('attendee')) {
						continue;
					}
				@endphp
				<div class="col-lg-4 col-md-4 col-sm-12">
					<div class="speaker-item">
						<div class="image">
							<img src="{{ asset($organizer->profile_picture_path) }}" style="width:100%; height:336px; " alt="organizer" class="img-fluid">
							<div class="primary-overlay"></div>
							<div class="socials">
								<ul class="list-inline">
									<li class="list-inline-item text-white"><h3>{{ $loop->iteration }}</h3></li>
									<li class="list-inline-item text-white">{{ $organizer->full_name }}</li>
									<li class="list-inline-item text-white">{{ $organizer->email }}</li>
									<li class="list-inline-item text-white"><strong>{{ $organizer?->organized_events_count }}</strong> Events Organized</li>
								</ul>
							</div>
						</div>
						<div class="content text-center">
							<h5><a href="single-speaker.html">{{ $organizer->full_name }}</a></h5>
							<p>{{ $organizer?->organization?->name }} - {{ $organizer?->organization?->department }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

	<div class="container top-attendees">
		<div class="row">
			<div class="col-12">
				<!-- Section Title -->
				<div class="section-title white">
					<h3>Top <span class="alternate">Attendees</span></h3>
					<p>Recognize our esteemed community of attendees who have actively participated in numerous events, embodying a passion for engagement and a desire for unforgettable experiences.</p>
				</div>
			</div>
		</div>
		<div class="row">

		@foreach ($topAttendees as $attendee)
				@php
					if($organizer->hasRole('admin') || $organizer->hasRole('organizer')) {
						continue;
					}
				@endphp
				<div class="col-lg-4 col-md-4 col-sm-12">
					<div class="speaker-item">
						<div class="image">
							<img src="{{ asset($attendee->profile_picture_path) }}" style="width:100%; height:336px; " alt="organizer" class="img-fluid">
							<div class="primary-overlay"></div>
							<div class="socials">
								<ul class="list-inline">
									<li class="list-inline-item text-white"><h3>{{ $loop->iteration }}</h3></li>
									<li class="list-inline-item text-white">{{ $attendee->full_name }}</li>
									<li class="list-inline-item text-white">{{ $attendee->email }}</li>
									<li class="list-inline-item text-white"><strong>{{ $attendee->attended_events_count }}</strong> Events Organized</li>
								</ul>
							</div>
						</div>
						<div class="content text-center">
							<h5><a href="single-speaker.html">{{ $attendee->full_name }}</a></h5>
							<p>{{ $attendee->attendee_organization_name }} - {{ $attendee->attendee_occupation }}</p>
						</div>
					</div>
				</div>
			@endforeach

		</div>
	</div>
</section>

<section class="section about">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6 align-self-center">
				<div class="image-block bg-about">
					<img class="img-fluid" src="images/speakers/featured-speaker.jpg" alt="">
				</div>
			</div>
			<div class="col-lg-8 col-md-6 align-self-center">
				<div class="content-block">
					<h2>About The <span class="alternate">Eduvent</span></h2>
					<div class="description-one">
						<p>
							Eduvent is an event management platform for organizing and promoting events conducted by organizations and Higher Educational Institutions.
						</p>
					</div>
					<div class="description-two">
						<p>It provides students and professionals access to in-house and virtual events and an avenue for professional development.</p>
					</div>
					{{-- <ul class="list-inline">
						<li class="list-inline-item">
							<a href="#" class="btn btn-main-md">Buy ticket</a>
						</li>
						<li class="list-inline-item">
							<a href="#" class="btn btn-transparent-md">Read more</a>
						</li>
					</ul> --}}
				</div>
			</div>
		</div>
	</div>
</section>
@endsection