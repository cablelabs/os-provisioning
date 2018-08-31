{{--

Group blade: expandable group box, like panel, but without droppable

@vars:
	$header: 	Panel Header
	$content: 	the yield content section varibale
				NOTE: take care to not overwrite other vars, like content_1
	$expand:	if true, panel is expanded, default: is _not_ expanded

--}}

	<div class="panel panel-inverse">
		<div class="panel-heading">
			<h3 class="panel-title">
				<a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{$content}}" aria-expanded="false">
					<i class="fa fa-plus-circle pull-right"></i>
					{{$header}}
				</a>
			</h3>
		</div>
		<div id="{{$content}}" class="panel-collapse collapse {{(isset($expand) && $expand ? 'show' : '') }}" aria-expanded="true" style="">
			<div class="panel-body">
				@yield($content)
			</div>
		</div>
	</div>
