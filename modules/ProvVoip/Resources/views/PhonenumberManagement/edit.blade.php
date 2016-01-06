@extends ('Generic.edit')

@section ('content_envia')
	
	<a href="google.de">Test</a>

@stop

@section ('content_right')
	@include ('bootstrap.panel', array ('content' => "content_envia", 'view_header' => 'Actions To Envia', 'md' => 6))
@stop

