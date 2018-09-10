{{--

The generic ip access-list bundle blade

--}}

ip access-list extended bundle_in_acl
 remark Bundle-in-ACL
@foreach($cb->ippools()->where('type', '=', 'CM')->get() as $cm_pool)
 permit ip any host {{$cm_pool->router_ip}}
@endforeach
 deny   ip any 10.0.0.0 0.255.255.255
 deny   ip any 100.64.0.0 0.63.255.255
@foreach($cb->ippools()->where('type', '=', 'MTA')->get() as $mta_pool)
 deny   ip any {{$mta_pool->net}} {{$mta_pool->wildcard_mask()}}
@endforeach
 permit ip any any
access-list compiled
