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

<a href="/admin/createCSV" class="btn btn-primary">{{ trans('view.Button_downloadCSV') }}</a>

<!-- Table -->
<table class="table table-hover table-bordered">
	<thead>
		<tr>
			@foreach ([trans('messages.Week'), trans('messages.Balance')] as $column => $name)
				<th scope="col" rowspan="2" class="text-center" width="20">{{$name}}</th>
			@endforeach
			@foreach (['Internet', 'VoIP', 'TV', trans('view.dashboard.other')] as $column => $value)
				<th scope="col" colspan="2" class="text-center">{{ $value }}</th>
			@endforeach
		</tr>
		<tr>
			@foreach (['Internet', 'VoIP', 'TV', 'Other'] as $column)
				<th width="20" class="text-center"><font color="green">+</font></th>
				<th width="20" class="text-center"><font color="red">-</font></th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@for($i = 0; $i <= 3; $i++)
			<tr>
				@foreach ([$contracts_data['table']['weekly']['week'], $contracts_data['table']['weekly']['ratio'], $contracts_data['table']['weekly']['gain']['Internet'], $contracts_data['table']['weekly']['loss']['Internet'], $contracts_data['table']['weekly']['gain']['Voip'], $contracts_data['table']['weekly']['loss']['Voip'], $contracts_data['table']['weekly']['gain']['TV'], $contracts_data['table']['weekly']['loss']['TV'], $contracts_data['table']['weekly']['gain']['Other'], $contracts_data['table']['weekly']['loss']['Other']] as $value)
					<td class="text-center">{{$value[$i]}}</td>
				@endforeach
			</tr>
		@endfor
	</tbody>
</table>
