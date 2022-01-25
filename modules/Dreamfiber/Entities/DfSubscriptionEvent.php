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

namespace Modules\Dreamfiber\Entities;

class DfSubscriptionEvent extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'dfsubscriptionevent';

    protected $fillable = [];

    /**
     * View Stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'Dreamfiber subscription event';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-pencil-square-o"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => [
                $this->table.'.id',
                $this->table.'.timestamp',
                $this->table.'.status',
            ],
            'header' => $this->label(),
            'bsclass' => $this->get_bsclass(),
            'edit' => [],
        ];
    }

    public function get_bsclass()
    {
        return match ($this->status) {
            'ACCEPTED-C' => 'warning',
            'ACCEPTED-O' => 'warning',
            'CEASE' => 'warning',
            'COMMISSION' => 'warning',
            'DECOMMISSION' => 'warning',
            'DELETED' => 'info',
            'EXCEPTION-PC' => 'danger',
            'EXCEPTION-PD' => 'danger',
            'NEW' => 'warning',
            'REJECTED-C' => 'danger',
            'REJECTED-O' => 'danger',
            'RUNNING' => 'success',
            'TERMINATED' => 'info',
            default => 'info',
        };
    }

    public function label()
    {
        return "$this->status – $this->timestamp";
    }

    public function view_belongs_to()
    {
        return $this->dfsubscription;
    }

    public function dfsubscription()
    {
        return $this->belongsTo(\Modules\Dreamfiber\Entities\DfSubscription::class, 'subscription_id');
    }
}
