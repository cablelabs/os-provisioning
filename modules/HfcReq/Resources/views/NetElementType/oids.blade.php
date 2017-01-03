<!-- attach button -->
<div class='col-md-6'>
	{{ Form::open(['route' => ['NetElementType.assign', $view_var->id], 'method' => 'get']) }}
		{{ Form::submit('Assign OIDs', ['style' => 'simple']) }} <!-- .\App\Http\Controllers\BaseViewController::translate($view) -->
	{{ Form::close() }}
</div>

<!-- detach all button -->
<div class='col-md-6'>
	{{ Form::open(['route' => ['NetElementType.detach_all', $view_var->id], 'method' => 'delete']) }}
		{{ Form::submit('Detach All OIDs', ['!class' => 'btn btn-danger', 'style' => 'simple']) }} <!-- .\App\Http\Controllers\BaseViewController::translate($view) -->
	{{ Form::close() }}
</div>

<br><br>

<!-- list and detach button -->
{{ Form::open(array('route' => array('NetElementType.detach_oid', $view_var->id), 'method' => 'delete')) }}

	<br>
	<table class="table">
		@foreach ($list as $oid)
			<tr class="{{isset ($oid->view_index_label()['bsclass']) ? $oid->view_index_label()['bsclass'] : ''}}">
				<td> {{ Form::checkbox('ids['.$oid->id.']', 1, null, null, ['style' => 'simple']) }} </td>
				<td> {{ HTML::linkRoute('OID.edit', is_array($oid->view_index_label()) ? $oid->view_index_label()['header'] : $oid->view_index_label(), $oid->id) }} </td>
			</tr>
		@endforeach
	</table>

	<!-- detach button -->
	<div class='col-md-12'>
		{{ Form::submit('Detach', ['!class' => 'btn btn-danger', 'style' => 'simple']) }}
	</div>

{{ Form::close() }}
