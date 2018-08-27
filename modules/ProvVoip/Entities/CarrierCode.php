<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in nmsprime root dir
class CarrierCode extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'carriercode';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
        ];
    }

    // Don't forget to fill this array
    protected $fillable = ['carrier_code', 'company'];

    public static function is_valid($carrier_code)
    {
        $pattern = '#^D[0-9a-fA-F]{3}$#';

        return boolval(preg_match($pattern, $carrier_code));
    }

    /**
     * Return a list [db_id => carrier (carriercode)] of all carriers.
     * This list is prepared for the use in a form's select
     *	- first comes the default value (=no carrier)
     *	- second are the favorites
     *	- then the rest
     *
     *	@author Patrick Reichel
     *
     *	@return array containing all carriers
     */
    public static function carrier_list_for_form_select($with_empty = true)
    {

        // carrier codes of the carriers to be on top of the list
        // the given sorting will be the sorting of the <select>
        // TODO: maybe this list should not be hardcoded – can come from configuration dialog or out of .env?
        if (\Module::collections()->has('ProvVoipEnvia')) {
            $favorite_carriers = [
                'D057', // envia TEL; has to be used if no porting is wanted (new number from envia TEL)
                'D001', // Telekom
                'D201', // 1&1
            ];
        } else {
            $favorite_carriers = [
                'D001', // Telekom
                'D201', // 1&1
            ];
        }

        $normal_carrier_list = [];
        $favorite_carrier_list = [];
        $no_carrier_list = [];

        // get all the carrier code data and put it into arrays
        foreach (self::orderBy('company')->orderBy('carrier_code')->get() as $carrier) {
            // assign to helper vars to make the rest of the loop easier to understand
            $id = $carrier->id;
            $carrier_code = $carrier->carrier_code;
            $company = $carrier->company;

            // create the string to be shown as <option> in <select>
            $entry = $company;
            if (boolval($carrier_code)) {
                $entry .= ' ('.$carrier_code.')';
            }

            // helper to check if current carrier is favorite and – if yes – in which position it shall occur
            $fav_pos = array_search($carrier_code, $favorite_carriers);

            // add to the corresponding array
            if (! boolval($carrier_code)) {
                $no_carrier_list[$id] = $entry;
            } elseif ($fav_pos !== false) {
                // indirect assignment – array key is the position in $favorite_carriers; database_id is stored separately
                $favorite_carrier_list[$fav_pos] = [$id, $entry];
            } else {
                // direct assignment
                $normal_carrier_list[$id] = $entry;
            }
        }

        // build result array (with favorites on top)
        // attention! don't change the keys – they refer directly to ids in table carriercode!
        // therefore we cannot use array_merge, but have to do it by hand!

        // first element is the null element ⇒ this will be selected by default in a new form
        // so we initialize the result array with the null element
        if ($with_empty) {
            $result = $no_carrier_list;
        } else {
            $result = [];
        }

        // sort favorites by key (=position in favorite_carriers) to get correct order
        ksort($favorite_carrier_list);

        // then add the favorites sorted like in favorite_carriers
        foreach ($favorite_carrier_list as $fav) {
            $id = $fav[0];	// database ID
            $entry = $fav[1];	// string to be shown in <option>
            $result[$id] = $entry;
        }

        // finally add the rest of the carriers
        // as mentioned above: this has to be done manually to take care of the IDs!
        foreach ($normal_carrier_list as $id => $entry) {
            $result[$id] = $entry;
        }

        return $result;
    }
}
