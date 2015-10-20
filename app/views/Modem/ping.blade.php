@extends ('layouts.single')

@include ('modems.header')

@section('content_left')
		@foreach ($ret as $line)

				<table>
				<tr>
					<td> 
						{{$line}}
					</td>
				</tr>

				</table>
			
		@endforeach
@stop