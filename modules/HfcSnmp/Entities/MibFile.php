<?php

namespace Modules\HfcSnmp\Entities;

class MibFile extends \BaseModel {

	public $table = 'mibfile';

	public $guarded = ['mibfile_upload'];


	/**
	 * @Const MibFile Upload Path relativ to storage directory
	 */
	const REL_MIB_UPLOAD_PATH = 'app/data/hfcsnmp/mibs/';


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'filename' => 'unique:mibfile,filename,'.$id.',id,deleted_at,NULL',
		);
	}

	/**
	 * View specific Stuff
	 */
	// Name of View
	public static function view_headline()
	{
		return 'MIB-File';
	}

	// link title in index view
	public function view_index_label()
	{
		// TODO: possible Colorization: red - MIBs that occur multiple times - but checking can decrease performance dramatically
		return ['index' => [$this->id, $this->name, $this->version],
				'index_header' => ['ID', 'Name', 'Version'],
				'header' => $this->name];
	}


	/**
	 * Relations
	 */
	public function oids()
	{
		return $this->hasMany('Modules\HfcSnmp\Entities\OID', 'mibfile_id')->orderBy('oid');
	}


	public function view_has_many ()
	{
		return array(
			'OID' => $this->oids,
		);
	}



	/**
	 * Boot: init observer
	 */
	public static function boot()
	{
		parent::boot();

		MibFile::observe(new MibFileObserver);
	}


	public function get_full_filepath()
	{
		return storage_path(self::REL_MIB_UPLOAD_PATH).$this->filename;
		// return storage_path(self::REL_MIB_UPLOAD_PATH).$this->name.'_'.$this->version.'.mib';
	}


	/**
	 * Create OID Database Entries from parsing snmptranslate outputs of all OIDs of the MIB
	 * Extract informations of OID: name, syntax, access, description
	 *
	 * @author Nino Ryschawy
	 */
	public function create_oids()
	{
		$abs_filepath = $this->get_full_filepath();
		// $abs_filepath = \Request::file('mibfile_upload')->path(); 		// if still in /tmp
		// $filetext = file_get_contents($abs_filepath);


		// check necessary? - Note: exception is bad response for user of running/production system
		if (!is_file($abs_filepath))
			$this->error("Upload File not yet written");


		// Get all OIDs of MIB - this includes many OIDs from the MIBs that are included in this MIB			
		exec("snmptranslate -To -m $abs_filepath 2>&1", $oids); 			// 2>&1 ... stderr to stdout


		// check if Translation of MIB is dependent of another MIB
		// TODO: We need a solution for a better response than an exception as the error msg is not readable on production systems (where APP_DEBUG=false)
		if (isset($oids[1]) && strpos($oids[1], "Cannot find module") !== false)
		{
			preg_match('#\((.*?)\)#', substr($oids[1], 18), $mib);
			$msg = "Please load dependent '".$mib[1].'\' before!! (OIDs cant be translated otherwise)';
			$this->error($msg);

			// $this->description = $msg;
			// $this->save();
			// $c = new \Modules\HfcSnmp\Http\Controllers\MibFileController;
			// return $c->create();
			// return \Redirect::back()->with('message', $msg)->with('message_color', 'red');
			// return \Redirect::route('MibFile.create')->with('message', $msg)->with('message_color', 'blue');
		}


		// Parse and Create all OIDs that really belong to this MIB
		foreach($oids as $oid)
		{
			$out = [];
			$error = false;
			$name = $syntax = $access = $description = '';

			// $out = shell_exec("snmptranslate -Td -m $abs_filepath $oid");
			exec("snmptranslate -Td -m $abs_filepath $oid", $out);

			if (!isset($out[0]))
				continue;

			// check if OID belongs to current uploaded MIB-File (exclude OIDs from included MIBs)
			if ($this->name != substr($out[0], 0, strpos($out[0], '::')))
				continue;

			foreach ($out as $key => $line)
			{
				// name
				if ($key == 1)
				{
					$tmp = explode(' ', $line);
					if ($tmp[1] != 'OBJECT-TYPE')
					{
						$error = true;
						break;
					}
					$name = $tmp[0];

					// skip OIDs from other (dependent) MIBs (name not existent in file) listet in $out - now done before foreach
					// if (strpos($filetext, $name) === false)
					// {
					// 	$error = true;
					// 	break;
					// }
				}

				// syntax
				if (strpos($line, 'SYNTAX') !== false)
				{
					$tmp 	= explode("\t", $line);
					if (isset($tmp[1]))
						$syntax = trim($tmp[1]);
					else
						break;
				}

				// access
				if (strpos($line, 'MAX-ACCESS') !== false)
				{
					$tmp 	= explode("\t", $line);
					if (isset($tmp[1]))
						$access = trim($tmp[1]);
					else
						break;
				}

				// description
				if (strpos($line, 'DESCRIPTION') !== false)
				{
					$tmp = implode($out);
					if (($end = strpos($tmp, 'DEFVAL')) === false)
					{
						if (($end = strpos($tmp, '::=')) === false)
							$end = null;
					}
					$description = substr($tmp, 14, $end ? $end - 14 : null);
					$description = str_replace("\t", '', $description);
				}

				unset($out[$key]);
			}

			if ($error || !$syntax || !$access)
				continue;

			// create OID
			OID::create([
				'mibfile_id' => $this->id,
				'oid' => $oid,
				'name' => $name,
				'access' => $access,
				'syntax' => $syntax,
				'description' => $description
			]);
		}

	}


	/**
	 * For use in Observer only - TODO: move to Observer
	 */
	private function error($message)
	{
		$this->delete();
		throw new \Exception($message);
	}

	/**
	 * Recursive Deletion of related OIDs - use this function as generic recursive deletion is super slow in this case!
	 *
	 * NOTE: generic recursive Deletion is disabled in BaseModel@get_all_children() by added exceptional column name: mibfile_id
	 */
	public function hard_delete_oids()
	{
		\DB::statement('DELETE from oid WHERE mibfile_id='.$this->id);
	}

}


class MibFileObserver
{

	public function creating($mibfile)
	{

	}

	public function created($mibfile)
	{
		if (\Request::hasFile('mibfile_upload'))
			$mibfile->create_oids();
	}

	public function updating($mibfile)
	{
	}

	public function deleting($mibfile)
	{
		// hard delete OIDs as Database becomes huge otherwise
		$mibfile->hard_delete_oids();
		
		// TODO: Unlink file ??
	}

}