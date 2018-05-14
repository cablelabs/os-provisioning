<?php
	// Colorization - this differs from index View as we can just consider Priority here
	$bsclass = array(
		'Trivial' 	=> 'active',
		'Minor' 	=> 'info',
		'Major' 	=> 'warning',
		'Critical' 	=> 'danger',
		);
?>

<div id="anchor-tickets">
<table class="table table-hover">
	<thead>
		<tr>
			<th>{{ \App\Http\Controllers\BaseViewController::translate_label('Title') }}</th>
			<th>{{ \App\Http\Controllers\BaseViewController::translate_label('Priority') }}</th>
		</tr>
	</thead>
	<tbody>
	@foreach ($data['tickets']['table'] as $ticket)
		<tr class = "{{$bsclass[$ticket->priority]}} clickableRow">
			<td class="ClickableTd">{{HTML::linkRoute('Ticket.edit', $ticket->name, $ticket->id)}}</td>
			<td class="ClickableTd">{{$ticket->priority}}</td>
		</tr>
	@endforeach
	</tbody>
</table>

</div>
