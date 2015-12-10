{{ Form::openGroup('name', 'Name') }}
{{ Form::text ('name') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('type', 'Type') }}
{{ Form::select ('type', array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('device', 'Device') }}
{{ Form::select('device', array('cm' => 'CM', 'mta' => 'MTA')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('parent_id', 'Parent Configfile') }}
{{ Form::select('parent_id', $parents) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('public', 'Public Use') }}
{{ Form::select ('public', array('yes' => 'Yes', 'no' => 'No')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('text', 'Config File Parameters') }}
{{ Form::textarea ('text', null, ['size' => '100x30']) }}
{{ Form::closeGroup() }}