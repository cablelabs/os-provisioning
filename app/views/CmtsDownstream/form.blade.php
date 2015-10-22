<table>


<tr>
	<td>{{ Form::label('cmts_id', 'CMTS') }}</td>
	<td>{{ Form::text ('cmts_id') }}</td> 
	<td>{{ $errors->first('cmts_id') }}</td>
</tr>

<tr>
	<td>{{ Form::label('index', 'Index') }}</td>
	<td>{{ Form::text ('index') }}</td> 
	<td>{{ $errors->first('index') }}</td>
</tr>

@foreach ($objects as $object)


	<tr>
		<td>{{ Form::label($object[1], $object[1]) }}</td>
	
		@if ($object[0] == 'input')
			<td>{{ Form::text ($object[1]) }}</td> 
		@endif

		@if ($object[0] == 'select')
			<td>{{ Form::select ($object[1], 
								 array_combine (array_values($object[4]),
								 				array_values($object[4]))) }}
			</td> 
		@endif

		<td>{{ $errors->first($object[1]) }}</td>
	</tr>

@endforeach

</table>