<?php

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;

/**
 * Configfile Observer Class
 * Handles changes on CMs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ConfigfileObserver
{
    public function created($configfile)
    {
        $this->updateProvision($configfile, false);
        // When a Configfile was created we can not already have a relation - so dont call command
    }

    public function updated($configfile)
    {
        $this->updateProvision($configfile, false);

        \Queue::push(new \Modules\ProvBase\Jobs\ConfigfileJob(null, $configfile->id));
        // $configfile->build_corresponding_configfiles();
        // with parameter one the children are built
        // $configfile->search_children(1);
    }

    public function deleted($configfile)
    {
        $this->updateProvision($configfile, true);
        // Actually it's only possible to delete configfiles that are not related to any cm/mta - so no CFs need to be built

        // Make sure that undeleted children still show up in tree
        $childrenQuery = Configfile::where('parent_id', $configfile->id);
        $children = $childrenQuery->get();

        $childrenQuery->update(['parent_id' => $configfile->parent_id]);

        foreach ($children as $child) {
            \Queue::push(new \Modules\ProvBase\Jobs\ConfigfileJob(null, $child->id));
        }
    }

    /**
     * Update monitoring provision of the corresponding configfile.
     *
     * The provision is assigned to every tr069 device having this configfile.
     * This makes sure, that we retrieve all objects to be monitored during every PERIODIC INFORM.
     *
     * @author Ole Ernst
     */
    private function updateProvision($configfile, $deleted)
    {
        // always delete provision, GenieACS doesn't mind deleting non-exisiting provisions
        // this way we don't need to care for a dirty $configfile->device
        Modem::callGenieAcsApi("provisions/mon-$configfile->id", 'DELETE');

        // nothing to do
        if ($deleted || $configfile->device != 'tr069') {
            return;
        }

        $prov = [];
        $conf = $configfile->getMonitoringConfig();

        $prov = array_map(function ($value) {
            if (\Str::startsWith($value, ['_', 'Device', 'InternetGatewayDevice'])) {
                return "declare('$value', {value: Date.now() - (290 * 1000)});";
            }
        }, \Illuminate\Support\Arr::flatten($conf));

        Modem::callGenieAcsApi("provisions/mon-$configfile->id", 'PUT', implode("\r\n", $prov));
    }
}
