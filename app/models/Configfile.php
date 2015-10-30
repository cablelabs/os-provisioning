<?php

namespace Models;

use Log;
use DB;
use Schema;

class Configfile extends \BaseModel {

    // The associated SQL table for this Model
    protected $table = 'configfile';


	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
            'name' => 'required|unique:configfiles,name,'.$id,
			// TODO: adapt docsis validator for mta files
            'name' => 'required|unique:configfile,name,'.$id,
            // 'text' => 'docsis'
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['name', 'text', 'device', 'type', 'parent_id', 'public', 'firmware'];


    // Name of View
    public static function get_view_header()
    {
        return 'Configfiles';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->name;
    }

    /**
     * TODO: make one function
     * returns a list of possible parent configfiles
     * Nearly the same like html_list method of BaseModel but needs zero element in front
     */
    public function parents_list ()
    {
        $parents = array('0' => 'Null');
		foreach (Configfile::all() as $cf)
		{
			if ($cf->id != $this->id)
				$parents[$cf->id] = $cf->name;
		}
		return $parents;
    }

    public function parents_list_all ()
    {
        $parents = array('0' => 'Null');
		foreach (Configfile::all() as $cf)
		{
			$parents[$cf->id] = $cf->name;
		}
		return $parents;
    }


	/**
	 * Returns all available firmware files (via directory listing)
	 * @author Patrick Reichel
	 */
	public function firmware_files() 
	{
		// get all available files
		$firmware_files_raw = glob("/tftpboot/fw/*");
		$firmware_files = array(null => "None");
		// extract filename
		foreach ($firmware_files_raw as $file) {
			if (is_file($file)) {
				$parts = explode("/", $file);
				$filename = array_pop($parts);
				$firmware_files[$filename] = $filename;
			}
		}
		return $firmware_files;
	}



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
		if (!$device)
			return false;

		/*
		 * all objects must be an array like a[xyz] = object
		 *
		 * INFO:
		 * - variable names _must_ match tables_a[xyz] coloumn
		 * - if modem sql relations are not valid a warning will
		 *   be printed
		 */
		$modem  = array ($device);
		$qos    = array ($device->qos);

		/*
		 * generate Table array with SQL columns
		 */
		$tables_a ['modem'][0] = Schema::getColumnListing('modem');
		$tables_a ['qos'][0]   = Schema::getColumnListing('qos');

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
				$modem    = array ($device);
				$quality = array ($device->quality);

				// write table descriptions to array
				$db_schemata ['modem'][0]    = Schema::getColumnListing('modem');
				$db_schemata ['quality'][0] = Schema::getColumnListing('quality');
				break;

			// this is for mtas
			case "mta":

				// same as above – arrays for later generic use
				// their have to match
				$mtas = array($device);
				$phonenumber = array($device->phonenumber);
				$phonenumber = $phonenumber[0];

				// get desription of table mtas
				$db_schemata['mta'][0] = Schema::getColumnListing('mta');
				// get description of table phonennumbers; one subarray per (possible) number
				for ($i = 0; $i < count($phonenumber); $i++) {
					$db_schemata['phonenumber'][$i] = Schema::getColumnListing('phonenumber');
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

		// DEBUG: print_r($search); print_r($replace);

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
