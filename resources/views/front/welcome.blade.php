@extends('layouts.app')

@section('content')

<!--============================
=            Banner            =
=============================-->

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

<!--====  End of Banner  ====-->


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


<!--===========================
=            About            =
============================-->

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

{{--
<!--====  End of About  ====-->

<!--==============================================
=            Call to Action Subscribe            =
===============================================-->

<section class="cta-subscribe bg-subscribe overlay-dark">
	<div class="container">
		<div class="row">
			<div class="col-md-6 mr-auto">
				<!-- Subscribe Content -->
				<div class="content">
					<h3>Subscribe to Our <span class="alternate">Newsletter</span></h3>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusm tempor</p>
				</div>
			</div>
			<div class="col-md-6 ml-auto align-self-center">
				<!-- Subscription form -->
				<form action="#" class="row">
					<div class="col-lg-8 col-md-12">
						<input type="email" class="form-control main white mb-lg-0" placeholder="Email">
					</div>
					<div class="col-lg-4 col-md-12">
						<div class="subscribe-button">
							<button class="btn btn-main-md">Subscribe</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<!--====  End of Call to Action Subscribe  ====-->

<!--================================
=            Google Map            =
=================================-->
<section class="map">
	<!-- Google Map -->
	<div id="map" data-latitude="40.712776" data-longitude="-74.005974" data-marker="images/icon/marker.png" data-marker-name="Eventre"></div>
	<div class="address-block">
		<h4>Docklands Convention</h4>
		<ul class="address-list p-0 m-0">
			<li><i class="fa fa-home"></i><span>Street Address, Location, <br>City, Country.</span></li>
			<li><i class="fa fa-phone"></i><span>[00] 000 000 000</span></li>
		</ul>
		<a href="#" class="btn btn-white-md">Get Direction</a>
	</div>
</section>
<!--====  End of Google Map  ====-->
 --}}
@endsection