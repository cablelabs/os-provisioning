<table class="table">

	<tr class='success'>
		<th>Cluster</th>
		<th>DSxUS Setting</th>
	</tr>

	@foreach ($rf->clusters as $cluster)
		<tr class='info'>
		<td>{{ HTML::linkRoute('NetElement.edit', $cluster->name, $cluster->id) }}</td>
		<td>{{$cluster->get_options_array()[$cluster->options]}}</td>
		</tr>
	@endforeach

</table>
