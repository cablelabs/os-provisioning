@extends ('Layout.single')

@include ('provbase::Modem.header')

@section('content_left')

<iframe frameborder="0" scrolling="no" width=100% height=5000
	src="../../../cacti/graph_view.php?action=preview&filter={{$modem->hostname}}" name="imgbox" id="imgbox">
</iframe>

@stop