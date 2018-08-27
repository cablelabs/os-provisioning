<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in nmsprime root dir
class EkpCode extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'ekpcode';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
        ];
    }

    // Don't forget to fill this array
    protected $fillable = ['ekp_code', 'company'];

    // TODO: who is a valid EKP code built?
    /* public static function is_valid($ekp_code) { */

    /* 	$pattern = '#^D[0-9a-fA-F]{3}$#'; */

    /* 	return boolval(preg_match($pattern, $ekp_code)); */
    /* } */

    /**
     * Return a list [db_id => ekp (ekpcode)] of all ekps.
     * This list is prepared for the use in a form's select
     *	- first come the favorites
     *	- then the rest
     *
     *	@author Patrick Reichel
     *
     *	@return array containing all ekps
     */
    public static function ekp_list_for_form_select()
    {

        // ekp codes of the ekps to be on top of the list
        // the given sorting will be the sorting of the <select>
        // TODO: maybe this list should not be hardcoded – can come from configuration dialog or out of .env?
        if (\Module::collections()->has('ProvVoipEnvia')) {
            $favorite_ekps = [
                '98/112', // envia TEL
                '93/007', // Telekom
                '12/017', // 1&1
                'n.v.',	// no EKP known
            ];
        } else {
            $favorite_ekps = [
                '93/007', // Telekom
                '12/017', // 1&1
                'n.v.',	// no EKP known
            ];
        }

        $normal_ekp_list = [];
        $favorite_ekp_list = [];

        // get all the ekp code data and put it into arrays
        foreach (self::orderBy('company')->orderBy('ekp_code')->get() as $ekp) {
            // assign to helper vars to make the rest of the loop easier to understand
            $id = $ekp->id;
            $ekp_code = $ekp->ekp_code;
            $company = $ekp->company;

            // create the string to be shown as <option> in <select>
            $entry = $company;
            if (boolval($ekp_code)) {
                $entry .= ' ('.$ekp_code.')';
            }

            // helper to check if current ekp is favorite and – if yes – in which position it shall occur
            $fav_pos = array_search($ekp_code, $favorite_ekps);

            // add to the corresponding array
            if ($fav_pos !== false) {
                // indirect assignment – array key is the position in $favorite_ekps; database_id is stored separately
                $favorite_ekp_list[$fav_pos] = [$id, $entry];
            } else {
                // direct assignment
                $normal_ekp_list[$id] = $entry;
            }
        }

        // build result array (with favorites on top)
        // attention! don't change the keys – they refer directly to ids in table ekpcode!
        // therefore we cannot use array_merge, but have to do it by hand!

        $result = [];

        // sort favorites by key (=position in favorite_ekps) to get correct order
        ksort($favorite_ekp_list);

        // then add the favorites sorted like in favorite_ekps
        foreach ($favorite_ekp_list as $fav) {
            $id = $fav[0];	// database ID
            $entry = $fav[1];	// string to be shown in <option>
            $result[$id] = $entry;
        }

        // finally add the rest of the ekps
        // as mentioned above: this has to be done manually to take care of the IDs!
        foreach ($normal_ekp_list as $id => $entry) {
            $result[$id] = $entry;
        }

        return $result;
    }
}
