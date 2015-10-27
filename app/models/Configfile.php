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
		$type = strtolower($type);
		if (!$device)
			return false;

		switch ($type) {

		case "modem":

			/*
			 * all objects must be an array like a[xyz] = object
			 *
			 * INFO:
			 * - variable names _must_ match tables_a[xyz] coloumn
			 * - if modem sql relations are not valid a warning will
			 *   be printed
			 */
			$modems    = array ($device);
			$qualities = array ($device->quality);

			/*
			 * generate Table array with SQL columns
			 */
			$db_schemata ['modems'][0]    = Schema::getColumnListing('modems');
			$db_schemata ['modems'][1]    = Schema::getColumnListing('modems');
			$db_schemata ['qualities'][0] = Schema::getColumnListing('qualities');
			break;

		case "mta":
			break;

		default:
			return false;

		}

		return $this->__text_make_now($device, $type, $db_schemata);
	}

	private function __text_make_now ($device, $type, $db_schemata)
	{
		/*
		 * Generate search and replace array
		 */
		$search = array();
		$replace = array();

		$i = 0;
		foreach ($db_schemata as $table => $columns_multi)
		{
PENG!
echo "<pre>"; dd($table); echo "</pre>";

			foreach ($columns_multi as $j => $columns)
			{
				if (isset(${$table}[$j]->id))
				{
					$replace_a = DB::select ("SELECT * FROM ".$table." WHERE id = ?", array(${$table}[$j]->id))[0];

					foreach ($columns as $entry)
					{
						$search[$i]  = '{'.$table.'.'.$entry.'.'.$j.'}';
						$replace[$i] = $replace_a->{$entry};

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
