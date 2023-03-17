<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Modem;
use Queue;

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

        Queue::pushOn('high', new \Modules\ProvBase\Jobs\ConfigfileJob(null, $configfile->id));
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
            Queue::pushOn('high', new \Modules\ProvBase\Jobs\ConfigfileJob(null, $child->id));
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
        if ($deleted || $configfile->device != 'tr069' || ($entries = $configfile->getMonitoringConfig()) == []) {
            return;
        }

        $entries = $configfile->getValidMonitoringEntries();
        $entries['columns'] = Configfile::getMonitoringColumns();

        Modem::callGenieAcsApi(
            "provisions/mon-{$configfile->id}",
            'PUT',
            view('provbase::GenieACS.monitoring', $entries)->render()
        );
    }
}
