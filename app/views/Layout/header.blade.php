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
	<hr><hr>


	<div class="col-md-6">
		@yield('content_top')
	</div>
	
	<div class="col-md-6">
		<p align="right">
			@yield('content_top_2')
		</p>
	</div>
	<hr>
