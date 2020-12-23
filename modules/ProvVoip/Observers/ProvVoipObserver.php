<?php

namespace Modules\ProvVoip\Observers;

class ProvVoipObserver
{
    public function updated($provvoip)
    {
        \Modules\ProvBase\Entities\ProvBase::first()->make_dhcp_glob_conf();
    }
}
