{{ Form::openGroup('hostname', 'Hostname') }}
{{ Form::text ('hostname') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('mac', 'MAC address') }}
{{ Form::text ('mac') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('description', 'Description') }}
{{ Form::textarea('description') }}
{{ Form::closeGroup() }}