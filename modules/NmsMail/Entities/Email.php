<?php

namespace Modules\NmsMail\Entities;

use App\Http\Controllers\BaseViewController;

class Email extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'email';

    // Name of View
    public static function view_headline()
    {
        return 'Email';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-envelope-o"></i>';
    }

    // There are no validation rules
    public static function rules($id = null)
    {
        return [
            'localpart' => 'regex:/^[0-9A-Za-z\.\-\_]+$/|required|max:64|unique:email,localpart,'.$id.',id,deleted_at,NULL',
            'domain_id' => 'required',
            'index' => 'integer|required',
            'forwardto' => 'email',
        ];
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
                'index_header' => [$this->table.'.localpart', $this->table.'.index',  $this->table.'.greylisting',  $this->table.'.blacklisting',  $this->table.'.forwardto'],
                //'header' =>  $this->index.': '.$this->localpart.'@'.isset($this->domain) ? $this->domain->name : 'no-domain.com' ,
                'bsclass' => $bsclass,
                'order_by' => ['1' => 'asc'],
                'eager_loading' => ['domain'], ];
    }

    public function get_bsclass()
    {
        $bsclass = $this->index ? 'success' : 'danger';

        return $bsclass;
    }

    public function view_belongs_to()
    {
        return $this->contract;
    }

    public function contract()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\Contract');
    }

    public function domain()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\Domain');
    }

    /**
     * Update the email password
     * A salted sha512 hash is used instead of bcrypt (not in mainline glibc)
     *
     * @param psw: password
     *
     * @author Ole Ernst
     */
    public function psw_update($psw)
    {
        $salt = str_replace('+', '.', base64_encode(random_bytes(12)));
        $this->password = crypt($psw, sprintf('$6$%s$', $salt));
        $this->save();
    }

    /**
     * Returns the type of an email address, which is derived from its index
     *
     * @author Ole Ernst
     */
    public function get_type()
    {
        switch ($this->index) {
            case 0:
                return BaseViewController::translate_label('disabled');
            case 1:
                return BaseViewController::translate_label('primary');
            default:
                return BaseViewController::translate_label('secondary');
        }
    }
}
