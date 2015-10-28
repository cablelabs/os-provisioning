<?php

namespace Models;

use Log;
use DB;
use Schema;

class Configfile extends \Eloquent {

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
            'name' => 'required|unique:configfiles,name,'.$id,
			// TODO: apapt docsis validator for mta files
            'text' => 'docsis'
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['name', 'text', 'device', 'type', 'parent_id', 'public'];


    /**
     * all Relationships:
     */
	public function modem ()
	{
		return $this->hasMany('Models\Modem');
	}

	public function get_parent ()
	{
		return Configfile::find($this->parent_id);
	}


    /**
     * Internal Helper:
     *   Make Configfile Content for $this Object /
     *   without recursive objects
     */
	private function __text_make ($device, $type)
	{
		// normalize type
		$type = strtolower($type);

		// we need a device to make the config for
		if (!$device)
			return false;

		// using the given type we decide what to do
		switch ($type) {

			// this is for modem's config files
			case "modem":

				/*
				 * all objects must be an array like a[xyz] = object
				 *
				 * INFO:
				 * - variable names _must_ match database table names and key in db_schemata[key (later we will use this array vars through dynamic variable names calling them by the current table name]
				 * - if modem sql relations are not valid a warning will
				 *   be printed
				 */
				$modems    = array ($device);
				$qualities = array ($device->quality);

				// write table descriptions to array
				$db_schemata ['modems'][0]    = Schema::getColumnListing('modems');
				$db_schemata ['qualities'][0] = Schema::getColumnListing('qualities');
				break;

			// this is for mtas
			case "mta":

				// same as above – arrays for later generic use
				// their have to match
				$mtas = array($device);
				$phonenumbers = array($device->phonenumbers);
				$phonenumbers = $phonenumbers[0];

				// get desription of table mtas
				$db_schemata['mtas'][0] = Schema::getColumnListing('mtas');
				// get description of table phonennumbers; one subarray per (possible) number
				for ($i = 0; $i < count($phonenumbers); $i++) {
					$db_schemata['phonenumbers'][$i] = Schema::getColumnListing('phonenumbers');
				}
				break;

			// this is for unknown types – atm we do nothing
			default:
				return false;

		}	// switch

		// Generate search and replace arrays
		$search = array();
		$replace = array();

		$i = 0;

		// loop over all schemata; they can exist multiple times per table
		foreach ($db_schemata as $table => $columns_multiple)
		{
			// loop over all schema descriptions of the current table
			foreach ($columns_multiple as $j => $columns)
			{
				// use the data arrays created before, calling them by current table name
				// fill temporary replacement array with database values
				if (isset(${$table}[$j]->id))
				{
					$replace_tmp = DB::select ("SELECT * FROM ".$table." WHERE id = ?", array(${$table}[$j]->id))[0];

					// loop over each column and check if there is something to replace
					// column is used generic to get values
					foreach ($columns as $column)
					{
						$search[$i]  = '{'.$table.'.'.$column.'.'.$j.'}';
						$replace[$i] = $replace_tmp->{$column};

						$i++;
					}
				}
				else
					Log::warning ($type.' '.$device->hostname.' has no valid '.$table.' entry');
			}
		}

		/*
		 * Search and Replace Configfile TEXT
		 */
		$text = str_replace($search, $replace, $this->text);
		$rows = explode("\n", $text);

		/*
		 * Delete all with {xyz} content which can not be replaced
		 */
		$result = '';
		foreach ($rows as $row)
			if (!preg_match("/\\{[^\\{]*\\}/im", $row))
				$result .= "\n\t".$row;

		/*
		 * return
		 */
		return $result;
	}

    /**
     * Make Configfile Content
     */
	public function text_make($device, $type)
	{
		$p = $this;
		$t = '';

		do {
			$t .= $p->__text_make($device, $type);
			$p  = $p->get_parent();
		} while ($p);

		return $t;
	}

}
