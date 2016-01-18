<?php

namespace Modules\ProvBase\Entities;

use Log;
use DB;
use Schema;

use Modules\ProvVoip\Entities\Phonenumber;

class Configfile extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'configfile';


    public $guarded = ['firmware_upload'];


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
     * BOOT:
     * - init configfile observer
     */
    public static function boot()
    {
        parent::boot();

        Configfile::observe(new ConfigfileObserver);
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
	 * Searches children of a parent configfile and returns them by pushing them to an array
	 * used for recursive building of the configfiles tree structure
     */
    public function search_children()
	{
		$id = $this->id;
		$cf_tree = $children = Configfile::all()->where('parent_id', $id)->all();

		foreach ($children as $key => $cf)
		{
			array_push($cf_tree, $cf->search_children());
		}

		return $cf_tree;
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
     * all Relationships
     *
     * Note: Should be plural on hasMany
     */
	public function modem ()
	{
		return $this->hasMany('Modules\ProvBase\Entities\Modem');
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
		// array to extend the configfile; e.g. for firmware
		$config_extensions = array();

		// normalize type
		$type = strtolower($type);
		// we need a device to make the config for
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


		// using the given type we decide what to do
		switch ($type) {

			// this is for modem's config files
			case "modem":

				$modem  = array ($device);
				$qos 	= array ($device->qos);

				/*
				 * generate Table array with SQL columns
				 */
				$db_schemata ['modem'][0] 	= Schema::getColumnListing('modem');
				$db_schemata ['qos'][0] 	= Schema::getColumnListing('qos');

				// if there is a specific firmware: add entries for upgrade
				if ($this->firmware) {
					// $server_ip = ProvBase::first()['provisioning_server'];
					// array_push($config_extensions, "SnmpMibObject docsDevSwServerAddress.0 IPAddress $server_ip ; /* tftp server */");
					array_push($config_extensions, 'SnmpMibObject docsDevSwFilename.0 String "fw/'.$this->firmware.'"; /* firmware file to download */');
					array_push($config_extensions, 'SnmpMibObject docsDevSwAdminStatus.0 Integer 2; /* allow provisioning upgrade */');
				}

				break;

			// this is for mtas
			case "mta":

				// same as above – arrays for later generic use
				// they have to match database table names
				$mta = array($device);
				$phonenumber = Phonenumber::where('mta_id', '=', $device->id)->get();

				// get description of table mtas
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

		// finally: append extensions; they have to be an array with one entry per line
		$rows = array_merge($rows, $config_extensions);

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


	/**
	* Build the configfiles of the appropriate modems and mtas after a configfile was updated/created/assigned
	*
	* @author Nino Ryschawy
	*/
	public function build_corresponding_configfiles()
	{
		// configfile itself
		$modems = $this->modem;

		foreach ($modems as $modem)
		{
			$modem->make_configfile();
			$mtas = $modem->mtas;		// This should be a one-to-one relation
			foreach ($mtas as $mta)
			{
				$mta->make_configfile();
			}
		}

		// children (the whole tree structure)
		$id = $this->id;
		do
		{
			// search for all configfiles that have this configfile as parent
			$children = Configfile::all()->where('parent_id', $id);

			foreach ($children as $child)
			{
				$modems = $child->modem;
				$id = $child->id;

				foreach ($modems as $modem)
				{
					$modem->make_configfile();
					$mtas = $modem->mtas;		// This should be a one-to-one relation
					foreach ($mtas as $mta)
					{
						$mta->make_configfile();
					}
				}
			}
		} while ($children->all());	
	}

}

/**
 * Configfile Observer Class
 * Handles changes on CMs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ConfigfileObserver
{
    public function created($configfile)
    {
		$configfile->build_corresponding_configfiles();
    }

    public function updated($configfile)
    {
		$configfile->build_corresponding_configfiles();
    }

    public function deleted($configfile)
    {
		$configfile->build_corresponding_configfiles();
    }
}