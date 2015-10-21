@section('content_top')

	{{ HTML::linkRoute('phonenumber.index', 'Phonenumbers') }} / {{ HTML::linkRoute('phonenumber.edit', "(".$phonenumber->country_code.") ".$phonenumber->prefix_number."/".$phonenumber->number, array($phonenumber->id)) }}


@stop
