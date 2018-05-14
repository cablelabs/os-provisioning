@extends('ccc::layouts.master')

@section('content_left')

	<table class="table table-bordered">
		@foreach($invoice_links as $key => $link)
			@if ($key % 2)
				<td class="{{$link['bsclass']}}" align="center"=> {{ $link['link'] }} </td></tr>
			@else
				<tr><td class="{{$link['bsclass']}}" align="center"> {{ $link['link'] }} </td>
			@endif
		@endforeach
	</table>

@stop

@section('content_emails')

	<table class="table">
		@foreach($emails as $email)
			<tr><td> {{ HTML::linkRoute('CustomerPsw', $email->view_index_label()['header'], ['email_id' => $email->id]) }} </td><td>{{ $email->get_type() }}</td></tr>
		@endforeach
	</table>

@stop

@section('content')

	@include ('bootstrap.panel', array ('content' => 'content_left', 'invoice_links' => $invoice_links, 'view_header' => trans('messages.Invoices'), 'md' => 4))

	@if (!$emails->isEmpty())
		@include ('bootstrap.panel', array ('content' => 'content_emails', 'emails' => $emails, 'view_header' => App\Http\Controllers\BaseViewController::translate_label('E-Mail Address'), 'md' => 4))
	@endif

@stop
