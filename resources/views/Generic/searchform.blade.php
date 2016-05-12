<?php
	// searchscope for following form is the current model
	if (!isset($next_scope))
		$next_scope = $route_name;

	$query = (isset($_GET['query']) ? $_GET['query'] : '');
	$button_text = (isset ($button_text) ? $button_text : ($next_scope == 'all' ? '?' : 'Search'));
?>



		{{-- in a first step use only simple search => later activate index search if simple version is to slow --}}
		{{ Form::hidden('mode', 'simple') }}
		{{ Form::hidden('scope', Str::lower($next_scope)) }}
		@if (isset($preselect_field))
			{{ Form::hidden('preselect_field', $preselect_field) }}
			{{ Form::hidden('preselect_value', $preselect_value) }}
		@endif

		{{--
			<span>{{ Form::radio('mode', 'simple', true) }}&nbsp;SIMPLE</span>
			<span>{{ Form::radio('mode', 'index_boolean') }}&nbsp;BOOL</span>
			<span>{{ Form::radio('mode', 'index_natural') }}&nbsp;NAT</span>
		--}}

	<input type="text" name="query" placeholder="Search .." class="form-control">


		{{-- no help needed for simple search
			<span><a href="https://mariadb.com/kb/en/mariadb/fulltext-index-overview" target="_blank">Search help</a>
		--}}
