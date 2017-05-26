@extends ('Layout.split84')

@section('content_top')

	<li class="active">{{ HTML::linkRoute($route_name.'.index', \App\Http\Controllers\BaseViewController::translate_view($route_name.'s', 'Header')) }}</li> <!--$view_header -->

@stop

@section('content_left')

	<!-- Search Field
	@DivOpen(12)
		@DivOpen(8)
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		@DivClose()
	@DivClose()  -->

	@DivOpen(12)
		@if ($create_allowed)
			{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				<button class="btn btn-primary m-b-15" style="simple">
					<i class="fa fa-plus fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Create Configfiles', 'Button' )}}
				</button>
			{{ Form::close() }}
		@endif
	@DivClose()

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete', 'onsubmit' => 'return submitMe()')) }}
			@include('provbase::Configfile.tree_hidden_helper', array('items' => $roots))
				<div id="jstree-default">
					<ul>
						<li data-jstree='{"icon":"fa fa-folder text-info fa-lg", "opened":true}' class="f-s-16 f-w-400 m-5 nocheck">{{ \App\Http\Controllers\BaseViewController::translate_view('Configfiles', 'Header', 2)}}
							@include('provbase::Configfile.tree_item', array('items' => $roots))
						</li>
					</ul>
				</div>

		<!-- delete/submit button of form -->
			<button class="btn btn-danger btn-primary m-r-5 m-t-15" style="simple">
					<i class="fa fa-trash-o fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button') }}
			</button>
			{{ Form::close() }}

	@DivClose()

@stop