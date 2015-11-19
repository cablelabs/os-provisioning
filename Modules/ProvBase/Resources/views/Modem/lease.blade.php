@extends ('Layout.single')

@include ('provbase::Modem.header')

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