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

namespace Modules\NmsMail\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Domain;

class EmailController extends \BaseController
{
    protected $index_create_allowed = false;

    public function view_form_fields($model = null)
    {
        // create: get contract from input
        if (\Request::get('contract_id')) {
            $contract = Contract::findOrFail(\Request::get('contract_id'));
        }
        // edit: get contract from model, takes precedence
        if ($model->contract) {
            $contract = $model->contract;
        }

        $used = [];
        // remove all email indices, which are already in use
        foreach ($contract->emails as $email) {
            // don't remove index, which is used by this model, as it would change the index if we hit the save button
            if ($email->index && $email->index != $model->index) {
                $used[] = $email->index;
            }
        }
        $avail = array_diff(range(0, $contract->get_email_count()), $used);

        return [
            ['form_type' => 'text', 'name' => 'contract_id', 'description' => 'Contract'],
            ['form_type' => 'text', 'name' => 'localpart', 'description' => 'Local Part'],
            ['form_type' => 'select', 'name' => 'domain_id', 'description' => 'Domain', 'value' => $model->html_list(Domain::where('type', '=', 'Email')->get(), 'name')],
            ['form_type' => 'text', 'name' => 'password', 'description' => 'Password', 'hidden' => 1],
            ['form_type' => 'select', 'name' => 'index', 'description' => 'Index', 'value' => $avail, 'help' => "0: disabled\n1: primary email address"],
            ['form_type' => 'checkbox', 'name' => 'greylisting', 'description' => 'Greylisting', 'value' => '1'],
            ['form_type' => 'checkbox', 'name' => 'blacklisting', 'description' => 'Blacklisting', 'value' => '1'],
            ['form_type' => 'text', 'name' => 'forwardto', 'description' => 'Forward To'],
        ];
    }
}
