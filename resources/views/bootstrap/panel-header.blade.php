		<div class="panel-heading d-flex flex-row justify-content-between">
			<h4 class="panel-title d-flex">
				@if (Str::contains($view_header, '<ul class="nav'))
					{!! $view_header !!}
				@else
					{{ \App\Http\Controllers\BaseViewController::translate_view( $view_header, 'Header', 2) }}
				@endif
			</h4>
			<div class="panel-heading-btn d-flex flex-row">
				<a href="javascript:;"
					class="btn btn-xs btn-icon btn-circle btn-default d-flex"
					data-click="panel-expand"
					style="justify-content: flex-end;align-items: center">
					<i class="fa fa-expand d-flex"></i>
				</a>
				<!--a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a-->
				<a href="javascript:;"
					class="btn btn-xs btn-icon btn-circle btn-warning d-flex"
					data-click="panel-collapse"
					style="justify-content: flex-end;align-items: center">
					<i class="fa fa-minus"></i>
				</a>
				<a href="javascript:;"
					class="btn btn-xs btn-icon btn-circle btn-danger d-flex"
					data-click="panel-remove"
					style="justify-content: flex-end;align-items: center">
					<i class="fa fa-times"></i>
				</a>
			</div>
		</div>
