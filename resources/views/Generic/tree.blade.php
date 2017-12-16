@extends ('Layout.split84-nopanel')

@section('content_top')

	<li class="active"><a href={{route($route_name.'.index')}}>
	{{\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null)}}
	{{ \App\Http\Controllers\BaseViewController::translate_view($route_name.'s', 'Header', 2) }}</a>
	</li>

@stop

@section('content_left')

	<!-- Search Field
	@DivOpen(12)
		@DivOpen(8)
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		@DivClose()
	@DivClose()-->

	@DivOpen(12)
		<h1 class="page-header">
		{{\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null)}}
		{{\App\Http\Controllers\BaseViewController::translate_view($route_name.'s', 'Header', 2) }}
		</h1>

		@if ($create_allowed)
			{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				<button class="btn btn-primary m-b-15" style="simple">
					<i class="fa fa-plus fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Create '.$route_name.'s', 'Button' )}}
				</button>
			{{ Form::close() }}
		@endif
	@DivClose()

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)
		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete', 'onsubmit' => 'return submitMe()')) }}
			@include('Generic.tree_hidden_helper', array('items' => $view_var))

			{{-- kept for reference
				<ul><li data-jstree='{"opened":true}' class="f-s-16 f-w-400 m-5 nocheck">{{ \App\Http\Controllers\BaseViewController::translate_view($route_name.'s', 'Header', 2)}}
			--}}

			<div id="jstree-default">
				@include('Generic.tree_item', array('items' => $view_var, 'color' => 0))
			</div>

			<!-- delete/submit button of form -->
			<button class="btn btn-danger btn-primary m-r-5 m-t-15" style="simple">
					<i class="fa fa-trash-o fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button') }}
			</button>
		{{ Form::close() }}
	@DivClose()

@stop

@section('javascript')
<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<script>

	/**
	 * Generate jsTree
	 *
	 * Documentation: https://www.jstree.com/demo/
	 *
	 * @author: Christian Schramm
	 */
	var makeJsTreeView = function() {
	  $('#jstree-default').jstree({
		  'plugins': [ "html_data", "checkbox", "wholerow", "types", "ui", "search", "state"],
		  "core": {
			  "dblclick_toggle": true,
			  "themes": {
				  "responsive": true,
			  }
		  },
		  "checkbox": {
			  "cascade": "",
			  "three_state": false,
			  "whole_node" : false,
			  "tie_selection" : false,
			  "real_checkboxes": true
		  },
		  "state" : { "filter" : function (k) { delete k.core.selected; return k; },
		  "key"   : "{{ $route_name }}",
		},
		  "types": {
			  "cm":{
				"icon": "fa fa-hdd-o text-warning fa-lg"
			  },
			  "mta": {
				"icon": "fa fa-fax text-info fa-lg"
			  },
			  "Net": {
				"icon": "fa fa-cloud text-info fa-lg"
			  },
			  "Cluster": {
				"icon": "fa fa-soundcloud text-warning fa-lg"
			  },
			  "Cmts": {
				"icon": "fa fa-building text-success fa-lg"
			  },
			  "Amplifier": {
				"icon": "fa fa-toggle-right text-danger fa-lg"
			  },
			  "Node": {
				"icon": "fa fa-arrow-circle-o-right text-warning fa-lg"
			  },
			  "Data": {
				"icon": "fa fa-server text-active fa-lg"
			  },
			  "default": {
				  "icon": "fa fa-file-code-o text-success fa-lg"
			  },
			  "default-2": {
				  "icon": "fa fa-file-excel-o text-warning fa-lg"
			  },
			  "default-3": {
				  "icon": "fa fa-file-pdf-o text-danger fa-lg"
			  },
			  "default-4": {
				  "icon": "fa fa-file-image-o text-info fa-lg"
			  }
		  }
	  });


	  $('#jstree-default').on('select_node.jstree', function(e,data) {
		  var link = data.node.a_attr.href;
		  if (link != "#" && link != "javascript:;" && link != "") {
			  document.location.href = link;
			  return false;
		  }
	  });


	// trigger on Checkbox change and give
	// invisible form the name of selected id
	// @author: Christian Schramm

	  $('#jstree-default').on("check_node.jstree uncheck_node.jstree", function (e, data) {
		  if (data.node.state.checked) {
			document.getElementById('myField'+ data.node.id).name = data.node.id;
		  } else {
			document.getElementById('myField'+ data.node.id).name = '';
		  }
	  });
	};


	$(document).ready(function() {
		makeJsTreeView();
	});

</script>
@stop
