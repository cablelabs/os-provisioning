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
			'name' => 'required|unique:configfile,name,'.$id.',id,deleted_at,NULL',
			'text' => "docsis",
			'cvc' => 'required_with:firmware',
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Configfiles';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-file-code-o"></i>'; 
	}

	// link title in index view
	public function view_index_label()
	{
		return $this->device.': '.$this->name;
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
	 * Searches children of a parent configfile recursivly to build the whole tree structure of all confifgfiles
	 *
	 * @author Nino Ryschawy
	 * @param boolean variable - if 1 all modems and mtas that belong to the configfile (and their children) are built
	 */
	public function search_children($build = 0)
	{
		$id = $this->id;
		// TODO: this should not be a database query
		$children = Configfile::all()->where('parent_id', $id)->all();
		$cf_tree = [];

		foreach ($children as $cf)
		{
			if ($build)
			{
				$cf->build_corresponding_configfiles();
				$cf->search_children(1);
			}
			else
			{
				array_push($cf_tree, $cf);
				array_push($cf_tree, $cf->search_children());
			}
		}

		return $cf_tree;
	}


	/**
	 * Returns all available files (via directory listing)
	 * @author Patrick Reichel
	 */
	public function get_files($folder)
	{
		// get all available files
		$files_raw = glob("/tftpboot/$folder/*");
		$files = array(null => "None");
		// extract filename
		foreach ($files_raw as $file) {
			if (is_file($file)) {
				$parts = explode("/", $file);
				$filename = array_pop($parts);
				$files[$filename] = $filename;
			}
		}
		return $files;
	}

	/**
	 * Returns text section of Code Validation Certificate in a human readable format
	 * while skipping non-relevant sections (i.e. hashes)
	 * @author Ole Ernst
	 */
	public function get_cvc_help() {
		if (!$this->cvc)
			return("The Code Validation Certificate 'cvc.der' can be extracted from a firmware image 'fw.img' by issuing:\n\nopenssl pkcs7 -print_certs -inform DER -in fw.img | openssl x509 -outform DER -out cvc.der");
		exec('openssl x509 -text -inform DER -in /tftpboot/cvc/'.$this->cvc, $cvc_help);
		return join("\n", array_slice($cvc_help, 0, 11));
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

	public function mtas ()
	{
		return $this->hasMany('Modules\ProvVoip\Entities\Mta');
	}

	public function children ()
	{
		return $this->hasMany('Modules\ProvBase\Entities\Configfile', 'parent_id');
	}

	public function parent ()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Configfile');
	}

	public function get_parent ()
	{
		return Configfile::find($this->parent_id);
	}


	/**
	 * Return all children Configfiles for $this Configfile
	 *
	 * Note: we return a normal array(). Eloquent->where(..)->get() does
	 *       return a special formated array, which does not work in
	 *       make_ordered_tree()
	 *
	 * @author Torsten Schmidt
	 *
	 * @return all children for $this configfile, null if no children
	 */
	public function get_children ()
	{
		$ret = [];

		foreach (Configfile::whereRaw('parent_id = '.$this->id)->get() as $a)
			array_push ($ret, $a);

		return $ret;
	}


	/**
	 * Return a recursive structured 1d-array for $cfgs Configfiles
	 * with adapt the Configfile names for Index view
	 *
	 * Note: This function is recursive style
	 *
	 * @author Torsten Schmidt
	 *
	 * @param the configfile's to structrue
	 * @return the structrued configfile for $cfgs with all children
	 */
	public function make_ordered_tree ($cfgs)
	{
		$ret = [];

		foreach($cfgs as $cfg)
		{
			if ($cfg->get_children())
			{
				// push all children and adapt name for index view
				array_push ($ret, $cfg);
				foreach ($this->make_ordered_tree($cfg->get_children()) as $a)
				{
					$a->name = '- - - - '.$a->name; // adapt name
					array_push ($ret, $a);
				}
			}
			else
				array_push ($ret, $cfg);
		}

		return $ret;
	}


	/*
	 * Return a pre-formated index list
	 */
	public function index_list ()
	{
		return $this->make_ordered_tree ($this->where('parent_id', '=', '0')->orderBy('device')->get());
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
					// array_push($config_extensions, 'SwUpgradeServer $server_ip;');
					array_push($config_extensions, 'SwUpgradeFilename "fw/'.$this->firmware.'";');
				}

				if ($this->cvc)
					exec("xxd -p -c 254 /tftpboot/cvc/".$this->cvc." | sed 's/^/MfgCVCData 0x/; s/$/;/'", $config_extensions);
				break;

			// this is for mtas
			case "mta":

				// same as above – arrays for later generic use
				// they have to match database table names
				$mta = array($device);
				// get description of table mtas
				$db_schemata['mta'][0] = Schema::getColumnListing('mta');

				// get Phonenumbers to MTA
				foreach (Phonenumber::where('mta_id', '=', $device->id)->orderBy('port')->get() as $phone)
				{
					$phone->active = ($phone->active ? 1 : 2);
					// use the port number as primary index key, so {phonenumber.number.1} will be the phone with port 1, not id 1 !
					$phonenumber[$phone->port] = $phone;
					// get description of table phonennumbers; one subarray per (possible) number
					$db_schemata['phonenumber'][$phone->port] = Schema::getColumnListing('phonenumber');
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

		// lo all schemata; they can exist multiple times per table
		foreach ($db_schemata as $table => $columns_multiple)
		{
			// loop over all schema descriptions of the current table
			foreach ($columns_multiple as $j => $columns)
			{
				// use the data arrays created before, calling them by current table name
				// fill temporary replacement array with database values
				if (isset(${$table}[$j]->id))
				{
					// loop over each column and check if there is something to replace
					// column is used generic to get values
					foreach ($columns as $column)
					{
						$search[$i]  = '{'.$table.'.'.$column.'.'.$j.'}';
						$replace[$i] = ${$table}[$j]->{$column};

						$i++;
					}
				}
				else
					Log::warning ($type.' '.$device->hostname.' has no valid '.$table.' entry');
			}
		}

		// DEBUG: var_dump ($search, $replace);

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
		$modems = $this->modem;
		foreach ($modems as $modem)
			$modem->make_configfile();

		$mtas = $this->mtas;		// This should be a one-to-one relation
		foreach ($mtas as $mta)
			$mta->make_configfile();
	}

	/**
	 * Recursively add all parents of a used node to the list of used nodes,
	 * we must not delete any of them
	 *
	 * @author Ole Ernst
	 */
	static protected function _add_parent(& $ids, $cf)
	{
		$parent = $cf->parent;
		if($parent && !in_array($parent->id, $ids)) {
			array_push($ids, $parent->id);
			self::_add_parent($ids, $parent);
		}
	}

	/**
	 * Returns a list of configfiles (incl. all of its parents), which are
	 * still assigned to a modem or mta and thus must not be deleted.
	 *
	 * @author Ole Ernst
	 */
	static public function all_in_use()
	{
		$used_ids = [];
		// only public configfiles can be assigned to a modem or mta
		foreach (Configfile::where('public', '=', 'yes')->get() as $cf) {
			if(count($cf->modem) || count($cf->mtas)) {
				array_push($used_ids, $cf->id);
				self::_add_parent($used_ids, $cf);
			}
		}

		return $used_ids;
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
		// with parameter one the children are built
		$configfile->search_children(1);
	}

	public function updated($configfile)
	{
		$configfile->build_corresponding_configfiles();
		$configfile->search_children(1);
	}

	public function deleted($configfile)
	{
		$configfile->build_corresponding_configfiles();
		$configfile->search_children(1);
	}
}
