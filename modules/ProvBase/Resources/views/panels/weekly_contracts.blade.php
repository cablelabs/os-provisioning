
<a href="/admin/createCSV" class="btn btn-primary">{{ trans('view.Button_downloadCSV') }}</a>

<!-- Table -->
<table class="table table-hover table-bordered">
	<thead>
		<tr>
			@foreach ([trans('messages.Week'), trans('messages.Balance')] as $column => $name)
				<th scope="col" rowspan="2" class="text-center" width="20">{{$name}}</th>
			@endforeach
			@foreach (['Internet', 'VoIP', 'TV', trans('view.Dashboard_Other')] as $column => $value)
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
				@foreach ([$contracts_data['table']['weekly']['week'], $contracts_data['table']['weekly']['ratio'], $contracts_data['table']['weekly']['gain']['internet'], $contracts_data['table']['weekly']['loss']['internet'], $contracts_data['table']['weekly']['gain']['voip'], $contracts_data['table']['weekly']['loss']['voip'], $contracts_data['table']['weekly']['gain']['tv'], $contracts_data['table']['weekly']['loss']['tv'], $contracts_data['table']['weekly']['gain']['other'], $contracts_data['table']['weekly']['loss']['other']] as $value)
					<td class="text-center">{{$value[$i]}}</td>
				@endforeach
			</tr>
		@endfor
	</tbody>
</table>
