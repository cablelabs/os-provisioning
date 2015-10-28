@extends ('Layout.single')

@include ('Modem.header')

@section('content_left')

		@foreach ($out as $line)

				<table>
				<tr>
					<td> 
						{{$line}}<br><br>
					</td>
				</tr>

				</table>
			
		@endforeach
@stop