@if (isset($extra_data) && !is_null($extra_data))

		@foreach($extra_data as $extra_content)

			{{-- new job class --}}
			@if (!array_key_exists('linktext', $extra_content))
				<br><b><u>
				{{ $extra_content['class'] }}
				</u></b><br>

			{{-- job is not possible ATM --}}
			@elseif (!array_key_exists('url', $extra_content))
				{{ $extra_content['linktext'] }} (not possible)
				<br>
			{{-- possible jobs --}}

			@else

				{{-- Check if help shall be shown --}}
				@if (array_key_exists('help', $extra_content))
					<span title="{{ $extra_content['help'] }}">
				@else
					<span>
				@endif

				@if ($extra_content['url'])
					<a href="{{ $extra_content['url'] }}">{{ $extra_content['linktext'] }}</a>
				@else
					{{ $extra_content['linktext'] }}
				@endif

				</span>

				<br>

			@endif

		@endforeach

@endif
