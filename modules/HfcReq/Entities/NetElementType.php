<?php

namespace Modules\HfcReq\Entities;

use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;

class NetElementType extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'netelementtype';

    private $max_parents = 15;

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * View Stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'NetElementType';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-object-group"></i>';
    }

    // icon type for tree view
    public function get_icon_type()
    {
        $type = $this->name ?: 'default';
        if ($parent = $this->parent) {
            $type = $parent->name;
            while ($parent = $parent->parent) {
                $type = $parent->name;
            }
        }

        return $type;
    }

    // link title in index view
    public function view_index_label()
    {
        // in Tree View returning an array is currently not yet implemented
        $version = $this->version ? ' - '.$this->version : '';

        return $this->name.$version;
    }

    // returns all objects that are related to a DeviceType
    public function view_has_many()
    {
        $ret['Edit']['NetElement']['class'] = 'NetElement';
        $ret['Edit']['NetElement']['relation'] = $this->netelements;

        if (\Module::collections()->has('HfcSnmp') && ! in_array($this->name, self::$undeletables)) {
            // $ret['Base']['Parameter']['class'] 	= 'Parameter';
            // $ret['Base']['Parameter']['relation']	= $this->parameters;

            // Extra view for easier attachment (e.g. attach all oids from one mibfile)
            $ret['Edit']['Parameters']['view']['view'] = 'hfcreq::NetElementType.parameters';
            $ret['Edit']['Parameters']['view']['vars']['list'] = $this->parameters ?: [];
            // Extra view for easier controlling view structure setting (html_frame, html_id of parameter)
            $ret['Parameter Settings']['Settings']['view']['view'] = 'hfcreq::NetElementType.settings';
            $ret['Parameter Settings']['Settings']['view']['vars']['list'] = self::param_list($this->id);
        }

        return $ret;
    }

    /**
     * Relations
     */
    public function netelements()
    {
        return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'netelementtype_id');
    }

    public function parameters()
    {
        return $this->hasMany('Modules\HfcSnmp\Entities\Parameter', 'netelementtype_id');
        // return $this->hasMany('Modules\HfcSnmp\Entities\Parameter', 'netelementtype_id')->orderBy('oid_id')->orderBy('id');
    }

    // only for preconfiguration of special device types (e.g. kathreins vgp)
    public function oid()
    {
        return $this->belongsTo('Modules\HfcSnmp\Entities\OID', 'pre_conf_oid_id');
    }

    public function parent()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElementType');
    }

    public function children()
    {
        return $this->hasMany('Modules\HfcReq\Entities\NetElementType', 'parent_id');
    }

    public static function param_list($id)
    {
        $eager_loading_model = new OID;
        $params = Parameter::where('netelementtype_id', '=', $id)->with($eager_loading_model->table)->get();
        $list = [];

        if (! $params) {
            return $list;
        }

        foreach ($params as $param) {
            $list[$param->id] = $param->oid->gui_name ? $param->oid->oid.' - '.$param->oid->gui_name : $param->oid->oid.' - '.$param->oid->name;
        }

        return $list;
    }

    /**
     * These Types are relevant for whole Entity Relation Diagram and therefore must not be deleted
     * Furthermore they are ordered by there Database ID which is probably used as fix value in many places of the source code
     * So don't change this order unless you definitly know what you are doing !!!
     */
    public static $undeletables = [1 => 'Net', 2 => 'Cluster', 3 => 'Cmts', 4 => 'Amplifier', 5 => 'Node', 6 => 'Data', 7 => 'UPS'];

    /**
     * Must be defined to disable delete Checkbox on index tree view.
     * Only deletable if there is no netelement assigned.
     *
     * @author Roy Schneider
     * @return array
     */
    public static function undeletables()
    {
        $used = [];
        $all = self::all();

        foreach ($all as $netelementtype) {
            if ($netelementtype->netelements()->count()) {
                $used[] = $netelementtype->id;
            }
        }

        return array_unique(array_merge(array_keys(self::$undeletables), $used));
    }

    /**
     * Return the base type id of the current NetElementType
     *
     * @note: base device means: parent_id = 0, 2 (cluster)
     *
     * @param
     * @return int id of base device netelementtype
     */
    public function get_base_type()
    {
        $p = $this;
        $i = 0;

        do {
            if (! is_object($p)) {
                return false;
            }

            if ($p->parent_id == 0 || $p->id == 2) { // exit: on base type, or cluster (which is child of net)
                return $p->id;
            }

            $p = $p->parent;
        } while ($i++ < $this->max_parents);

        return false;
    }
}
