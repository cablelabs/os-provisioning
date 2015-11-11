<div id="fulltext_search">
		<span>{{ Form::text('query', '', array('autofocus'=>'autofocus')) }}</span>
		{{-- in a first step use only simple search => later activate index search if simple version is to slow --}}
		<span>{{ Form::hidden('mode', 'simple') }}</span>
			{{--
			<span>{{ Form::radio('mode', 'simple', true) }}&nbsp;SIMPLE</span>
			<span>{{ Form::radio('mode', 'index_boolean') }}&nbsp;BOOL</span>
			<span>{{ Form::radio('mode', 'index_natural') }}&nbsp;NAT</span>
			--}}
		<span>{{ Form::hidden('scope', Str::lower($next_scope)) }}</span>
		<span>{{ Form::submit('Search '.Str::lower($next_scope)) }}</span>
			{{-- no help needed for simple search
			<span><a href="https://mariadb.com/kb/en/mariadb/fulltext-index-overview" target="_blank">Search help</a>
			--}}
</div>
