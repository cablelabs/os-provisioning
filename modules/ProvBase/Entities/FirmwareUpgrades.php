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

namespace Modules\ProvBase\Entities;

class FirmwareUpgrades extends \BaseModel
{
    public $table = 'firmware_upgrades';

    public static $name = 'Firmware Upgrade';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    public $guarded = [
        'fromconfigfile_ids',
    ];

    protected $fillable = [
        'start_date',
        'start_time',
        'finished_date',
        'cron_string',
        'batch_size',
        'to_configfile_id',
        'restart_only',
        'firmware_match_string',
    ];

    // Name of View
    public static function view_headline()
    {
        return self::$name;
    }

    // link title in index view
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => [
                $this->table.'.start_date',
                $this->table.'.start_time',
                $this->table.'.finished_date',
                $this->table.'.cron_string',
                $this->table.'.batch_size',
            ],
            // 'bsclass' => $bsclass,
            'header' => $this->label(),
            'edit' => [],
            'eager_loading' => ['configfile'],
            'globalFilter' => ['sw_rev' => e(session('filter_data', ''))],
        ];
    }

    public function label()
    {
        return $this->id;
    }

    /**
     * Relation to the pivot `firmware_upgrade_configfile` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function fromConfigfile()
    {
        return $this->belongsToMany(Configfile::class, 'firmware_upgrade_configfile', 'firmware_upgrade_id', 'configfile_id');
    }

    public function configfile()
    {
        return $this->belongsTo(Configfile::class, 'to_configfile_id');
    }

    /**
     * Format Configfile for edit view select field and allow for searching.
     *
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Configfiles(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return Configfile::select('id', 'name as text')
            ->withCount('firmwareUpgrades as count')
            ->when($search, function ($query, $search) {
                foreach (['name'] as $field) {
                    $query = $query->orWhere($field, 'like', "%{$search}%");
                }

                return $query;
            });
    }
}
