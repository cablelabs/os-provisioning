<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
@php
	$description['NetElementType'] = 'Parameter';
	$description['Parameter'] = 'SubOIDs';

	$model = NamespaceController::get_route_name();
@endphp


{{-- attach button --}}
<div class='col-md-6'>
	{!! Form::open(['route' => [$model.'.assign', $view_var->id], 'method' => 'get']) !!}
		{!! Form::submit(trans('view.Assign', ['model' => $description[$model]]), ['style' => 'simple']) !!}
	{{-- .\App\Http\Controllers\BaseViewController::translate($view) --}}
	{!! Form::close() !!}
</div>

{{-- detach all button --}}
<div class='col-md-6'>
	{!! Form::open(['route' => [$model.'.detach_all', $view_var->id], 'method' => 'delete']) !!}
		{!! Form::submit(trans('view.Detach all', ['model' => $description[$model]]), ['!class' => 'btn btn-danger', 'style' => 'simple']) !!}
	{!! Form::close() !!}
</div>

<br><br>

{{-- list and detach button --}}
{!! Form::open(array('route' => array('Parameter.destroy', 0), 'method' => 'delete')) !!}

	<br>
	<table class="table">
		@foreach ($list as $param)
			<tr class="{{isset ($param->view_index_label()['bsclass']) ? $param->view_index_label()['bsclass'] : ''}}">
				<td> {!! Form::checkbox('ids['.$param->id.']', 1, null, null, ['style' => 'simple']) !!} </td>
				<td> {!! HTML::linkRoute('Parameter.edit', is_array($param->view_index_label()) ? $param->view_index_label()['header'] : $param->view_index_label(), $param->id) !!} </td>
			</tr>
		@endforeach
	</table>

	{{-- Delete button --}}
	<div class='col-md-12'>
		{!! Form::submit('Delete', ['!class' => 'btn btn-danger', 'style' => 'simple']) !!}
	</div>

{!! Form::close() !!}
