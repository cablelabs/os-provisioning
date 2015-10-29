@extends('Generic.edit')
@section('content_right')
	
	<h2>IP Pools</h2>

	@foreach ($view_var->ippools as $pool)

		{{ HTML::linkRoute('IpPool.edit', $pool->id, $pool->id) }}
	
	@endforeach

@stop