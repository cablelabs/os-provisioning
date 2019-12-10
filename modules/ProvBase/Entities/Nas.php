<?php

namespace Modules\ProvBase\Entities;

class Nas extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'nas';

    public $timestamps = false;
    protected $forceDeleting = true;
    protected $guarded = ['created_at', 'updated_at', 'deleted_at', 'netgw_id'];

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }

    public static function rules($id = null)
    {
        return [
            'nasname' => 'required',
            'secret' => 'required',
        ];
    }

    public static function view_headline()
    {
        return 'Network Access Server';
    }

    public static function view_icon()
    {
        return '<i class="fa fa-unlock"></i>';
    }

    public function view_index_label()
    {
        return ['table' => $this->table,
                'index_header' => ["{$this->table}.nasname", 'netgw.hostname'],
                'header' =>  $this->nasname,
                'bsclass' => $this->get_bsclass(),
                'eager_loading' => ['netgw'],
        ];
    }

    public function get_bsclass()
    {
        return 'success';
    }

    public function view_belongs_to()
    {
        return $this->netgw;
    }

    public static function boot()
    {
        parent::boot();

        self::observe(new NasObserver);
    }

    public function netgw()
    {
        return $this->belongsTo(NetGw::class, 'shortname');
    }

    public function netgws()
    {
        return \DB::table($this->netgw()->getRelated()->table)->whereNull('deleted_at')->get();
    }
}

class NasObserver
{
    public function created($nas)
    {
        $this->reloadRadiusd();
    }

    public function updated($nas)
    {
        $this->reloadRadiusd();
    }

    public function deleted($nas)
    {
        $this->reloadRadiusd();
    }

    private function reloadRadiusd()
    {
        exec('sudo systemctl reload radiusd.service');
    }
}
