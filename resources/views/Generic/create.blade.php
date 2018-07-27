{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@extends ('Layout.split-nopanel')

@section('content_top')

	{!! $headline !!}
	<li><a onMouseOver="this.style.backgroundColor='#FFFFFF'"><span class="text-info">{{ \App\Http\Controllers\BaseViewController::translate_view('Create', 'Header') }}</span></a></li>

@stop


@section('content_left')

	{{ Form::open(array('route' => array($route_name.'.store', 0), 'method' => 'POST', 'files' => true)) }}

		@include($form_path)

	{{ Form::close() }}

@stop
