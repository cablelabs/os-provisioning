@extends ('Layout.default')

@section('content')

	<div class="col-md-12">

		<h1 class="page-header">{{ $title }}</h1>

		{{--Quickstart--}}

		<div class="row">
			<div class="col-md-12">
				@include('Generic.quickstart')
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-7">

			</div>
			<div class="col-md-5">
				@include('provvoip::widgets.documentation')
			</div>
		</div>
	</div>
	
@stop
