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
	protected $fillable = ['name', 'text', 'device', 'type', 'parent'];


	public function modem ()
	{
		return $this->hasMany('Models\Modem');
	}

	public function text_make ($m)
	{
		
		/*
		 *
		 */
		$modems    = array ($m);
		$endpoints = $m->endpoints;


		/*
		 *
		 */
		$tables_a ['modems'][0]    = Schema::getColumnListing('modems');

		$i = 0;
		foreach ($endpoints as $endpoint)
			$tables_a['endpoints'][$i++] = Schema::getColumnListing('endpoints');


		/*
		 *
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
					# _translation matrix
					$tlm[$i]['table']  = $name;
					$tlm[$i]['field']  = $entry;
					$tlm[$i]['search'] = $name.'.'.$entry;	
					$search[$i]        = '{'.$tlm[$i]['search'].'.'.$j.'}';

					$replace[$i] = $replace_a->{$entry};
					$i++;
				}
			}
		}	

		/* debug */
		// print_r($search); print_r($replace);

		/*
		 *
		 */		
		$text = str_replace($search, $replace, $this->text);
		$rows = explode("\n", $text);
		
		/*
		 *
		 */
		$result = '';
		foreach ($rows as $row)
			if (!preg_match("/\\{[^\\{]*\\}/im", $row))
				$result .= "\n".$row;
		
		return $result;
	}

}