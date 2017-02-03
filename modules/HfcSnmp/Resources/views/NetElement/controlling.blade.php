@extends ('Layout.default')


@section('content_top')

	{{ $headline }}

@stop



@section ('content')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

	<?php
		$i = 1;  // "frame" - index
		$last = '1';
	?>


	@section ('Content')

		<!-- Error | Success Message -->
		@if (Session::has('message'))
			@DivOpen(12)
				@if (Session::get('message_color') == 'blue')
					@DivOpen(5) @DivClose()
					@DivOpen(4)
				@endif
				<h4 style='color:{{ Session::get('message_color') }}' id='success_msg'>{{ Session::get('message') }}</h4>
				@if (Session::get('message_color') == 'blue')
					@DivClose()
				@endif
			@DivClose()
		@endif


		<!-- TABLE -->
		@foreach($form_tables as $table)
			{{ $table }}
		@endforeach

			<!-- FRAMES -->
		@foreach ($panel_form_fields as $form_fields)

			<?php
				$col_width = isset($columns) ? 12 / $columns[$form_fields[0]['panel'][0]] : 4;
			?>

			<div class="col-md-{{$col_width}} well" style="padding-right: 0px; padding-left: 0px;">

			<!-- Headline ?? -->

				@foreach($form_fields as $field)
					{{ $field['html'] }}
				@endforeach

			</div>

			@if ($mode == 'linear')
				<!-- break after 3 columns (default) -->
				@if (!($i % 3))
					<div class="col-md-12"><br></div>
				@endif
			@endif

			<?php 
				$i++;
				$last = $form_fields[0]['panel'][0];
			?>


		@endforeach

	{{-- Form::submit( \App\Http\Controllers\BaseViewController::translate_view($save_button , 'Button')) --}}


	<!-- Save Button -->
	<br>
	<div class="col-md-12">
		<div class="col-md-4"></div>
		<div class="col-md-2">
			<input class="btn btn-primary btn-block" style="simple" value="Save" type="submit">
		</div>
		<div class="col-md-5"></div>
	</div>

	@stop


	@include('bootstrap.panel', ['content' => 'Content', 'md' => 12])
	
	{{ Form::close() }}

	{{-- java script--}}
	@include('Generic.form-js')





	@if(0)
		<!-- temporary Notes -->
		<?php $last = 'table_1' ?>

		@foreach ($panel_form_fields as $index => $form_fields_row)

			<?php
				// new table
			// d($form_fields_row[key($form_fields_row)]['panel'], key($form_fields_row), $last);
				if ($last != $form_fields_row[key($form_fields_row)]['panel'])
				// if ($form_fields_row[key($form_fields_row)]['panel'] == 'table_2')
					$i = 1;
				echo '</tbody></table><br>';
			 ?>

			<!-- table head -->
			@if ($i == 1)
			<table class="table table-condensed">
			<thead>
				<tr role="row">
					<th> Index </th>
				@foreach ($form_fields_row as $oid => $form_fields)
					<th>{{ $form_fields['description'] }}</th>
				@endforeach
				</tr>
				<?php $i++ ?>
			</thead>
			<tbody>
			@endif

			<!-- table body -->
			<tr>
				<td> {{ $index }} </td>
			@foreach ($form_fields_row as $oid => $form_fields)
				<td>{{ $form_fields['html'] }}</td>
				<?php $last = $form_fields['panel'] ?>
			@endforeach
			</tr>


		@endforeach
			</tbody>
		</table>
	@endif

@stop