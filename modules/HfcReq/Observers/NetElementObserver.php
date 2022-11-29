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

namespace Modules\HfcReq\Observers;

use Module;
use Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Modules\HfcReq\Entities\NetElement;

class NetElementObserver
{
    public function creating($netelement)
    {
        if (! $netelement->base_type_id) {
            $netelement->base_type_id = $netelement->netelementtype->baseType->id;
        }
    }

    public function created($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        if ($netelement->hasInventoryTab() || $netelement->base_type_id == 22) {
            $netelement->getConnectedClass()::create(['netelement_id' => $netelement->id]);
        }

        $this->flushSidebarNetCache();

        if (Module::collections()->has('CoreMon')) {
            $netelement->createLink();

            return;
        }

        // in created because otherwise netelement does not have an ID yet
        $netelement->net = $netelement->get_native_net();
        $netelement->cluster = $netelement->get_native_cluster();
        $this->checkNetCluster($netelement);

        $netelement->observer_enabled = false;  // don't execute functions in updating again
        $netelement->save();
    }

    public function updating($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        if ($netelement->isDirty('parent_id', 'name')) {
            $this->flushSidebarNetCache();

            $netelement->net = $netelement->get_native_net();
            $netelement->cluster = $netelement->get_native_cluster();
            $netelement->base_type_id = $netelement->netelementtype->baseType->id;
            $this->checkNetCluster($netelement);

            // Change Net & cluster of all childrens too
            NetElement::whereDescendantOf($netelement->id)
                ->update([
                    'net' => $netelement->net,
                    'cluster' => $netelement->cluster,
                ]);

            // Change link
            if (Module::collections()->has('CoreMon')) {
                $linkQuery = \Modules\CoreMon\Entities\Link::where('from', $netelement->getRawOriginal('parent_id'))
                    ->where('to', $netelement->id);

                if ($netelement->parent_id) {
                    $linkQuery->update(['from' => $netelement->parent_id]);
                } else {
                    $linkQuery->delete();
                }
            }
        }

        // if netelementtype_id changes -> indices have to change there parameter id
        // otherwise they are not used anymore
        if ($netelement->isDirty('netelementtype_id')) {
            $netelement->base_type_id = $netelement->netelementtype->baseType->id;

            $new_params = $netelement->netelementtype->parameters;
            foreach ($netelement->indices as $indices) {
                // assign each indices of parameter to new parameter with same oid
                if ($new_params->contains('oid_id', $indices->parameter->oid->id)) {
                    $indices->parameter_id = $new_params->where('oid_id', $indices->parameter->oid->id)->first()->id;
                    $indices->save();
                } else {
                    // Show Alert that not all indices could be assigned to the new parameter -> user has to create new indices and delete the old ones
                    // We also could delete them directly, so that user has to add them again
                    Session::put('alert.info', trans('messages.indices_unassigned'));
                }
            }

            $this->handleTypeChangeForCoreMon($netelement);
        }
    }

    public function deleting($netelement)
    {
        if ($netelement->base_type_id == 18) {
            $netelement->connectedModel->delete();
        }
    }

    public function deleted($netelement)
    {
        $this->flushSidebarNetCache();

        if (Module::collections()->has('CoreMon')) {
            $netelement->links()->delete();
            $netelement->parent?->links()->where('to', $netelement->id)->delete();
        }
    }

    public function restored($netelement)
    {
        if (Module::collections()->has('CoreMon')) {
            \Modules\CoreMon\Entities\Link::withTrashed()->where('from', $netelement->id)
                ->orWhere('to', $netelement->id)->update(['deleted_at' => null]);
        }
    }

    protected function flushSidebarNetCache()
    {
        if ($user = auth()->user()) {
            Cache::forget($user->login_name.'-Nets');

            return;
        }

        return Artisan::call('cache:clear');
    }

    /**
     * Return error message when net or cluster ID couldn't be determined as this would result in hiding the element in the ERD
     */
    private function checkNetCluster($netelement)
    {
        if ($netelement->net) {
            return;
        }

        if ($netelement->parent_id && ! $netelement->net) {
            Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noNet'));
        }

        if ($netelement->parent_id && ! $netelement->cluster) {
            Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noCluster'));
        }
    }

    /**
     * Create new CoreMon model when netelementtype is changed and delete old one
     */
    private function handleTypeChangeForCoreMon($netelement)
    {
        // Create
        $clone = clone $netelement;
        $origAttrs = $clone->getRawOriginal();
        $clone->base_type_id = $origAttrs['base_type_id'];
        $newClass = $clone->getConnectedClass();
        $newClass::create(['netelement_id' => $clone->id]);

        // Delete
        $netelement->connectedModel->delete();
    }
}
