@extends ('Layout.single')

@include ('Modem.header')

@section('content_left')
		@foreach ($out as $line)

				<table>
				<tr>
					<td> 
						{{$line}}
					</td>
				</tr>

				</table>
			
		@endforeach
@stop