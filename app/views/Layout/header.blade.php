
	<h1 align=center>Das Monster</h1>

<p align=right>
	{{ HTML::linkRoute('Modem.index', 'Modems') }} |
	{{ HTML::linkRoute('Endpoint.index', 'Endpoints') }} |
	{{ HTML::linkRoute('Mta.index', 'MTAs') }} |
	{{ HTML::linkRoute('Phonenumber.index', 'Phonenumbers') }} |
	{{ HTML::linkRoute('Configfile.index', 'Configfiles') }} |
	{{ HTML::linkRoute('Qos.index', 'QoS') }} |
	{{ HTML::linkRoute('Cmts.index', 'CMTS') }} |
	{{ HTML::linkRoute('IpPool.index', 'IP-Pools') }}
	<?php
		// searchscope for following form is 'all' => search within all models
		$next_scope = 'all';
	?>
	{{ Form::model(null, array('route'=>$model_name.'.fulltextSearch', 'method'=>'GET')) }}
		@include('Generic.searchform')
	{{ Form::close() }}

</p>
