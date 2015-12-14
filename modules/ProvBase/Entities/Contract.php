<?php

namespace Modules\ProvBase\Entities;

use Modules\ProvBase\Entities\Qos;

class Contract extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'contract';

	// Don't forget to fill this array
	protected $fillable = [ 'number', 'number2', 'firstname', 'lastname', 'salutation', 'company', 'street', 'city', 'zip', 
        'country_id', 'x', 'y', 'phone', 'fax', 'email', 'birthday', 'contract_start', 'contract_end', 'network_access', 'qos_id', 
        'next_qos_id', 'voip_id', 'next_voip_id', 'sepa_iban', 'sepa_bic' , 'sepa_holder', 'sepa_institute', 'create_invoice', 
        'login', 'password', 'description' ];


	// Add your validation rules here
    public static function rules($id = null)
    {
        return array(
            'number' => 'integer|unique:contract,number,'.$id,
            'number2' => 'string|unique:contract,number2,'.$id,
            'firstname' => 'required',
            'lastname' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'email' => 'email',
            'birthday' => 'required|date',
            'contract_start' => 'date',
            'contract_end' => 'dateornull', // |after:now -> implies we can not change stuff in an out-dated contract
            'sepa_iban' => 'iban',
            'sepa_bic' => 'bic',
        );
    }
    
    
    // Name of View
    public static function get_view_header()
    {
        return 'Contract';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->id.' - '.$this->firstname.' '.$this->lastname.' - '.$this->city;
    }	


    // Relations
    public function modems()
    {
        return $this->hasMany('Modules\ProvBase\Entities\Modem');
    }

    // View Relation.
    public function view_has_many()
    {
        return array(
            'Modem' => $this->modems
        );
    }


    /*
     * Generate use a new user login password
     * This does not save the involved model
     */
    public function generate_password($length = 10)
    {
        $this->password = \Acme\php\Password::generate_password();
    }


    /**
     * BOOT:
     * - init observer
     */
    public static function boot()
    {
        parent::boot();

        Contract::observe(new ContractObserver);
    }

}


/**
 * Observer Class
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ContractObserver
{
    public function created($contract)
    {
        $contract->number = $contract->id;
        $contract->save();     // forces to call the updated method of the observer
    }

    public function updating($contract)
    {
        $contract->number = $contract->id;
    }
}