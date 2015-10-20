@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Cmts.index', 'CMTS') }} / {{ HTML::linkRoute('Cmts.edit', 'CMTS-'.$CmtsGw->hostname, $CmtsGw->id) }}
	
@stop

@section('content_left')

	<h2>Edit CMTS</h2>
	
	{{ Form::model($CmtsGw, array('route' => array('Cmts.update', $CmtsGw->id), 'method' => 'put')) }}

		@include('Cmts.form', $CmtsGw)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')
	
	<h2>IP Pools</h2>

	@foreach ($CmtsGw->ippools as $pool)

		{{-- HTML::linkRoute(Route, Name, Id als Variable in Url) --}}
		{{ HTML::linkRoute('IpPool.edit', $pool->id, $pool->id) }}
	
	@endforeach

@stop
