<?php

namespace Modules\HfcReq\Observers;

use Auth;
use Session;
use Illuminate\Support\Facades\Cache;
use Modules\HfcReq\Entities\NetElement;

class NetElementObserver
{
    public function created($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        $this->flushSidebarNetCache();

        // if ($netelement->is_type_cluster())
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
            $this->checkNetCluster($netelement);

            // Change Net & cluster of all childrens too
            NetElement::where('parent_id', '=', $netelement->id)
                ->update([
                    'net' => $netelement->net,
                    'cluster' => $netelement->cluster,
                ]);
        }

        // if netelementtype_id changes -> indices have to change there parameter id
        // otherwise they are not used anymore
        if ($netelement->isDirty('netelementtype_id')) {
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
        }
    }

    public function deleted()
    {
        $this->flushSidebarNetCache();
    }

    protected function flushSidebarNetCache()
    {
        Cache::forget(Auth::user()->login_name.'-Nets');
    }

    /**
     * Return error message when net or cluster ID couldn't be determined as this would result in hiding the element in the ERD
     */
    private function checkNetCluster($netelement)
    {
        if (! $netelement->net) {
            return Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noNet'));
        }

        if (! $netelement->net) {
            return Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noCluster'));
        }
    }
}
