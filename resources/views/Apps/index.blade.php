@extends ('Layout.split-nopanel')

@section ('content_left')
	@foreach ($apps as $app)
		<h4 style="text-align: center;"></h4>
		@foreach ($app as $category => $modules)
			<div class="btn noHover">
				<span>
					<b>{{ $category }}</b>
				</span>
				<div class="widget row" style="text-align: center;">
					@foreach ($modules as $attr)
						<div>
							<img src="{{ asset('images/apps/'.$attr['icon']) }}" style="height: 100px; margin-right: 10px; margin-left: 10px;">
							<p style="margin-top: 5px; color: black;">{{ $attr['name'] }}</p>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	@endforeach
@stop
