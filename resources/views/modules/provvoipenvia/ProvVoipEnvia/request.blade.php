@extends('Layout.split')

@section('content_left')
	@if (array_key_exists('plain_html', $view_var))

		{{ $view_var['plain_html'] }}

	@endif
@stop
