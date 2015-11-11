	@include ('bootstrap.menu', array(
		'header' => 'Das Monster', 
		'menus' => array (
			'Modems' => 'Modem.index',
			'Endpoints' => 'Endpoint.index',
			'Mta' => 'Mta.index',
			'Phonenumber' => 'Phonenumber.index',
			'Configfile' => 'Configfile.index',
			'QoS' => 'Qos.index',
			'CMTS' => 'Cmts.index',
			'Ip-Pool' => 'IpPool.index',
			'Device' => 'Device.index',
			'DeviceType' => 'DeviceType.index',
			'SnmpMib' => 'SnmpMib.index',
			'SnmpValue' => 'SnmpValue.index'
	)))
	<hr>
		<!-- TODO: Move global search form -->
		<?php
			// searchscope for following form is 'all' => search within all models
			$next_scope = 'all';
		?>
		{{ Form::model(null, array('route'=>$model_name.'.fulltextSearch', 'method'=>'GET')) }}
			@include('Generic.searchform')
		{{ Form::close() }}
	<hr>


	<div class="col-md-6">
		@yield('content_top')
	</div>
	
	<div class="col-md-6">
		<p align="right">
			@yield('content_top_2')
		</p>
	</div>
	<hr>
