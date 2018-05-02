@extends ('Layout.split84-nopanel')

@section('content_top')

<li class="active">
	<a href="{{route($route_name)}}">
	{!! \App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header !!}
	</a>
</li>

@stop


@section('content_left')

{{-- Headline: means icon followed by headline --}}
@DivOpen(12)
	<h1 class="page-header">
		{!! \App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header !!}
	</h1>

	<ul class="nav nav-pills d-flex nav-fill" id="SettingsTab">
		@foreach($module_model as $count => $model)
				<li class="nav-item"><a class="" href="#settings-{{Str::slug($links[$count]['name'],'_')}}" data-toggle="pill">
					{{ \App\Http\Controllers\BaseViewController::translate_label($links[$count]['name']) }} </a></li>
		@endforeach
	</ul>
@DivClose()

@DivOpen(12)
		<div class="tab-content">
			@foreach($module_model as $count => $model)
				<div class="tab-pane fade in" id="settings-{{Str::slug($links[$count]['name'],'_')}}" role="tabpanel">
					{!! Form::model($model, array('route' => array($links[$count]['link'].'.update', '1'), 'method' => 'put', 'files' => true) ) !!}

						@include('Generic.form',['view_var' => $model,
												 'form_fields' => $form_fields[$count],])
					{{ Form::close() }}
				</div>
			@endforeach
		</div>
@DivClose()
@stop
