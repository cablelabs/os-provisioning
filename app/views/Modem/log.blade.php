@extends ('layouts.single')

@include ('modems.header')

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