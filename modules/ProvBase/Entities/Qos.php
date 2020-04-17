<?php

namespace Modules\ProvBase\Entities;

class Qos extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'qos';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'name' => 'required',
            'ds_rate_max' => 'required|numeric|min:0',
            'us_rate_max' => 'required|numeric|min:0',
        ];
    }

    /**
     * Relations
     */
    public function modem()
    {
        return $this->hasMany(Modem::class);
    }

    public function prices()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\Price::class);
    }

    public function radgroupreplies()
    {
        return $this->hasMany(RadGroupReply::class, 'groupname');
    }

    public function products()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\Product::class);
    }

    // Name of View
    public static function view_headline()
    {
        return 'QoS';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-ticket"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
            'index_header' => [$this->table.'.name', $this->table.'.ds_rate_max', $this->table.'.us_rate_max'],
            'header' =>  $this->name,
            'bsclass' => $bsclass,
            'edit' => ['ds_rate_max' => 'unit_ds_rate_max', 'us_rate_max' => 'unit_us_rate_max'],
            'order_by' => ['0' => 'asc'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }

    public function unit_ds_rate_max()
    {
        return $this->ds_rate_max.' MBit/s';
    }

    public function unit_us_rate_max()
    {
        return $this->us_rate_max.' MBit/s';
    }

    public static function setIndexDeleteTitle()
    {
        return trans('messages.indexDeleteDisabledTitle', ['relation' => trans('messages.Product')]);
    }

    public function set_index_delete()
    {
        if (! \Module::collections()->has('BillingBase')) {
            return false;
        }

        $relatedProducts = $this->products()->whereNull('product.deleted_at')->count();

        return $this->index_delete_disabled = $relatedProducts >= 0;
    }

    /**
     * BOOT: init quality observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new QosObserver);
    }
}

/**
 * Qos Observer Class
 * Handles changes on QoSs
 */
class QosObserver
{
    public function __construct()
    {
        $file = storage_path('app/config/provbase/radius/attributes.php');
        $this->radiusAttributes = file_exists($file) ? require $file : RadGroupReply::$radiusAttributes;
    }

    public function created($qos)
    {
        foreach ($this->radiusAttributes as $key => $attributes) {
            foreach ($attributes as $attribute) {
                if (! $qos->{$key}) {
                    continue;
                }
                self::addRadGroupReply($qos, $attribute, $key);
            }
        }
    }

    public function updated($qos)
    {
        // update only ds/us if their values were changed
        foreach (array_intersect_key($this->radiusAttributes, $qos->getDirty()) as $key => $attributes) {
            foreach ($attributes as $attribute) {
                $reply = $qos->radgroupreplies()->where('attribute', $attribute[0])->where('value', 'like', $attribute[3]);

                // value might be null, since not all QoS vlaues are required (e.g. DS/US QoS name)
                if ($qos->{$key}) {
                    if ($reply->count()) {
                        $reply->update(['value' => sprintf($attribute[2], $qos->{$key})]);
                    } else {
                        self::addRadGroupReply($qos, $attribute, $key);
                    }
                } else {
                    $reply->delete();
                }
            }
        }
    }

    public function deleted($qos)
    {
        $qos->radgroupreplies()->delete();
    }

    /**
     * Add a RadGroupReply
     *
     * @author: Ole Ernst
     */
    private static function addRadGroupReply($qos, $attribute, $key)
    {
        $new = new RadGroupReply;
        $new->groupname = $qos->id;
        $new->attribute = $attribute[0];
        $new->op = $attribute[1];
        $new->value = sprintf($attribute[2], $qos->{$key});
        $new->save();
    }
}
