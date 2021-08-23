<?php

namespace Modules\ProvBase\Traits;

use Modules\ProvBase\Entities\Configfile;

trait HasConfigfile
{
    /**
     * Relation to Configfile table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Configfile::class);
    }

    /**
     * Format Configfiles for select 2 field and allow for seaching.
     *
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Configfiles(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return Configfile::select('id', 'name as text')
            ->when($this->table == 'modem', function ($query) {
                return $query->withCount(['modem as count']);
            })
            ->when($this->table == 'mta', function ($query) {
                return $query->withCount(['mtas as count']);
            })
            ->whereIn('device', $this->exists ? [$this->configfile->device] : static::TYPES)
            ->where('public', 'yes')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            });
    }

    /**
     * Deletes Configfile of one mta
     */
    public function delete_configfile()
    {
        $dir = static::CONFIGFILE_DIRECTORY;
        $file['1'] = $dir.static::CONFIGFILE_PREFIX.'-'.$this->id.'.cfg';
        $file['2'] = $dir.static::CONFIGFILE_PREFIX.'-'.$this->id.'.conf';

        foreach ($file as $f) {
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    public function assignedConfigfile()
    {
        $cf_name = 'No Configfile assigned';

        if (isset($this->configfile)) {
            $cf_name = $this->configfile->name;
        }

        return $cf_name;
    }
}
