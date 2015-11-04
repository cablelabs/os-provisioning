<div id="fulltext_search">
		<span>{{ Form::text('query') }}</span>
		<span>{{ Form::radio('mode', 'boolean', true) }}&nbsp;BOOL</span>
		<span>{{ Form::radio('mode', 'natural') }}&nbsp;NAT</span>
		<span>{{ Form::hidden('scope', 'n/a') }}</span>
		<span>{{ Form::submit('Search') }}</span>
		<span><a href="https://mariadb.com/kb/en/mariadb/fulltext-index-overview" target="_blank">Search help</a>
</div>
