@extends ('Layout.split84-nopanel')


@section('content_top')

<li class="active">
	<a href="{{route($route_name)}}">
	{{ \App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header}}
	</a>
</li>

@stop


@section('content_left')

<!-- Headline: means icon followed by headline -->
@DivOpen(12)
	<h1 class="page-header">
	{{\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header}}
	</h1>

	<ul class="nav nav-pills nav-justified p-b-5" id="SettingsTab">
		@foreach($links as $name => $link)
				<li role="presentation" class="nav-tabs"><a href="#settings-{{Str::slug($name,'_')}}" data-toggle="pill"> {{ \App\Http\Controllers\BaseViewController::translate_label($name) }} </a></li>
		@endforeach
	</ul>
@DivClose()
@DivOpen(12)
		<div class="tab-content">
			@foreach($links as $name => $link)
				<div class="tab-pane fade in" id="settings-{{Str::slug($name,'_')}}"></div>
			@endforeach
		</div>
@DivClose()
@stop
