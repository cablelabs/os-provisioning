@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('modem.index', 'Modems') }} / {{ HTML::linkRoute('modem.edit', 'Modem-'.$modem->hostname, array($modem->id)) }}

@stop

@section('content_left')
	
	{{ Form::model($modem, array('route' => array('modem.update', $modem->id), 'method' => 'put')) }}

		@include('modems.form', $modem)
	
	{{ Form::submit('Save') }}
	{{ Form::close() }}
@stop

@section('content_right')

	<h1>CPE Stuff ..</h1>

	{{ Form::open(array('route' => 'endpoint.create', 'method' => 'GET')) }}
	{{ Form::hidden ('modem_id', $modem->id) }}
	{{ Form::hidden ('modem_hostname', $modem->hostname) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}

	{{ Form::open(array('route' => array('endpoint.destroy', 0), 'method' => 'delete')) }}

		@foreach ($modem->endpoints as $endpoint)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$endpoint->id.']') }}
						{{ HTML::linkRoute('endpoint.edit', 'Endpoint-'.$endpoint->hostname, $endpoint->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop