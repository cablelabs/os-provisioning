@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Cmts.index', 'CMTS') }} / {{ HTML::linkRoute('Cmts.edit', 'CMTS-'.$cmts->hostname, $cmts->id) }}
	
@stop

@section('content_left')

	<h2>Edit CMTS</h2>
	
	{{ Form::model($cmts, array('route' => array('Cmts.update', $cmts->id), 'method' => 'put')) }}

		@include('Cmts.form', $cmts)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')
	
	<h2>IP Pools</h2>

	@foreach ($cmts->ippools as $pool)

		{{-- HTML::linkRoute(Route, Name, Id als Variable in Url) --}}
		{{ HTML::linkRoute('IpPool.edit', $pool->id, $pool->id) }}
	
	@endforeach

@stop
