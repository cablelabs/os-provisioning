<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>{{$html_title}}</title>
	@include ('bootstrap.header')
	@yield('head')
</head>
<body {{ isset($body_onload) ? "onload='$body_onload()'" : ""}}>

	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">

	@include ('Layout.header')

	@include ('bootstrap.sidebar')

		<div id="content" class="content p-t-15">
			@if(session('GlobalNotification'))
				@foreach (session('GlobalNotification') as $name => $options)
					<div class="alert alert-{{ $options['level'] }} alert-dismissible fade show" role="alert">
						<h4 class="text-center alert-heading">{{ trans('messages.' . $options['message']) }} </h4>
						<p class="mb-0 text-center">
							{{ trans('messages.' . $options['reason']) }}
							<a href="{{ route('User.profile', $user->id) }}" class="alert-link">
									{{ trans('messages.PasswordClick') }}
							</a>
						</p>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endforeach
			@endif
			<div class="row">
				@yield ('content')
			</div>
		</div>
	</div>

@include ('bootstrap.footer')
@yield ('form-javascript')
@yield ('javascript')
@yield ('javascript_extra')

{{-- scroll to top btn --}}
<a href="javascript:;"
	class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade d-flex"
	data-click="scroll-top"
	style="justify-content: space-around;align-items: center">
	<i class="fa fa-angle-up m-0"></i>
</a>

</body>
</html>
