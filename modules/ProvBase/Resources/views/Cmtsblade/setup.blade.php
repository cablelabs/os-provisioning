{{--

CMTS Setup Blade:
HOWTO: setup a new CMTS

--}}

<b>Setup command:</b><br>
@if (!\Modules\ProvBase\Entities\ProvBase::prov_ip_online())
	<div class="label label-danger">!!! Provisioning Server Management IP {!!$cb->prov_ip!!} Offline !!!</div><br><br>
@else
	<i>connect <b>{!!$cb->prov_if!!}</b> Interface with <b>{!!$cb->interface!!}</b> Interface</i><br><br>
@endif


{{-- CMTS setup code --}}
<pre>
interface {!!$cb->interface!!}
 ip address {!!$cb->ip!!} {!!$cb->netmask!!}
 no shutdown

copy tftp://{!!$cb->prov_ip!!}/cmts/{!!$cb->hostname!!}.cfg startup-config

reload
</pre>
