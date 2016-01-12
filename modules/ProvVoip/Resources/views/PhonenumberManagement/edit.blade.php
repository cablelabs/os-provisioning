@extends ('Generic.edit')

@section ('content_extra')

	@foreach($extra_data as $extra_content)

		{{-- new job class --}}
		@if (!array_key_exists('linktext', $extra_content))
			<br><b><u>
			{{ $extra_content['class'] }}
			</u></b><br>

		{{-- job is done --}}
		@elseif (!array_key_exists('url', $extra_content))
			{{ $extra_content['linktext'] }} ✔
			<br>
		{{-- possible jobs --}}

		@else
			<a href="{{ $extra_content['url'] }}">{{ $extra_content['linktext'] }}</a>
			<br>

		@endif

	@endforeach

@stop

@section ('content_right')
	@include ('bootstrap.panel', array ('content' => "content_extra", 'view_header' => 'Available actions against Envia API', 'md' => 6))
@stop

