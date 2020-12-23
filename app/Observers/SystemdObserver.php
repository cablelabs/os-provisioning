<?php

namespace App\Observers;

/**
 * Systemd Observer Class - Handles changes on Model Gateways - restarts system services
 *
 * TODO:
 * place it somewhere else ...
 * Calling this Observer is practically very bad in case there are more services inserted - then all services will restart even
 *		if Config didn't change - therefore a distinction is necessary - or more Observers,
 * another Suggestion:
 * place the restart file creation in the appropriate observer itself
 * only place a static function restart_dhcpd here that creates the file
 */
class SystemdObserver
{
    // insert all services that need to be restarted after a model changed there configuration in that array
    private $services = ['dhcpd', 'kea-dhcp6'];

    public function created($model)
    {
        \Log::debug('systemd: observer called from create context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function updated($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        // Exception - Dont restart dhcp server for modems where no relevant changes where made
        $model_name = new \ReflectionClass(get_class($model));
        $model_name = $model_name->getShortName();

        if ($model_name == 'Modem' && ! $model->needs_restart()) {
            return;
        }

        \Log::debug('systemd: observer called from update context', [$model_name, $model->id]);

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function deleted($model)
    {
        \Log::debug('systemd: observer called from delete context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }
}
