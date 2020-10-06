@extends ('Layout.split-nopanel')

@section ('content_left')
	@foreach ($apps as $app)
		<h4 style="text-align: center;"></h4>
		@foreach ($app as $category => $modules)
			<div class="btn">
				<span>
					<b>{{ $category }}</b>
				</span>
				<div class="widget row" style="text-align: center;">
					@foreach ($modules as $attr)
						<div>
							<a href="{{ $attr['link'] }}">
								<img title="{{ $attr['description'] }}" src="{{ asset('images/apps/'.$attr['icon']) }}" style="height: 100px; margin-right: 10px; margin-left: 10px;">
							</a>
							<p style="margin-top: 5px; color: black;">{{ $attr['name'] }}</p>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	@endforeach
@stop
