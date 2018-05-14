{{-- Error Message --}}
@if (Session::get('error_msg'))
	@DivOpen(12)
		<h5 style='color:red' id='delete_msg'>{{ Session::get('error_msg') }}</h5>
	@DivClose()
@endif

<h3>
	{{HTML::linkRoute('Contract.ConnInfo', 'Download', [$view_var->id])}}
</h3>