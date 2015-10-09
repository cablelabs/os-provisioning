@extends ('layouts.split')

@section('content_top')

	{{ HTML::linkRoute('cmts.index', 'CMTS') }} / {{ HTML::linkRoute('cmts.edit', 'CMTS-'.$CmtsGw->hostname, $CmtsGw->id) }}
	
@stop

@section('content_left')

	<h2>Edit CMTS</h2>
	
	{{ Form::model($CmtsGw, array('route' => array('cmts.update', $CmtsGw->id), 'method' => 'put')) }}

		@include('cmtsgws.form', $CmtsGw)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')
	
	<h2>IP Pools</h2>

	@foreach ($CmtsGw->ippools as $pool)

		{{-- HTML::linkRoute(Route, Name, Id als Variable in Url) --}}
		{{ HTML::linkRoute('ipPool.edit', $pool->id, $pool->id) }}
	
	@endforeach

@stop