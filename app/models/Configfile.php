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
            'name' => 'required|unique:configfiles,name,'.$id
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
	private function __text_make ($m)
	{
		if (!$m)
			return false;
		
		/*
		 * all objects must be an array like a[xyz] = object
		 * NOTE: 1. add new relations here
		 */
		$modems    = array ($m);
		$endpoints = $m->endpoints;

		/*
		 * generate Table array
		 * NOTE: 2. add new relations here
		 */
		$tables_a ['modems'][0]    = Schema::getColumnListing('modems');

		$i = 0;
		foreach ($endpoints as $endpoint)
			$tables_a['endpoints'][$i++] = Schema::getColumnListing('endpoints');


		/*
		 * Generate search and replace array
		 */
		$replace = array();

		$i = 0;
		foreach ($tables_a as $name => $tables) 
		{
			foreach ($tables as $j => $table)
			{
				$replace_a = DB::select ("SELECT * FROM ".$name." WHERE id = ?", array(${$name}[$j]->id))[0];
			
				foreach ($table as $entry)
				{
					$search[$i]  = '{'.$name.'.'.$entry.'.'.$j.'}';
					$replace[$i] = $replace_a->{$entry};
					
					$i++;
				}
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
	public function text_make($m)
	{
		$p = $this;
		$t = '';

		do {
			echo ($p->id);
			$t .= $p->__text_make($m);

			$p  = $p->get_parent();
		} while ($p);

		return $t;
	}

}