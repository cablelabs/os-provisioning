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

	public function text_make ($modems, $endpoints)
	{
		Log::debug('Log message Hostname'.$modems->hostname);

		// $tables = DB::select ("SELECT CONCAT (TABLE_NAME, "." ,COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS where Table_schema = 'db_lara'");

		$tables['modems']    = Schema::getColumnListing('modems');
		$tables['endpoints'] = Schema::getColumnListing('endpoints');

		$replace = array();

		$i = 0;
		foreach ($tables as $name => $table)
		{
			$replace_a = DB::select ("SELECT * FROM ".$name." WHERE id = ?", array(${$name}->id))[0];
		
			foreach ($table as $entry)
			{
				# _translation matrix
				$tlm[$i]['table']  = $name;
				$tlm[$i]['field']  = $entry;
				$tlm[$i]['search'] = $name.'.'.$entry;	
				$search[$i]        = '$'.$tlm[$i]['search'].'$';

				$replace[$i] = $replace_a->{$entry};
				$i++;
			}


		}

		// debug:
		// print_r($search); print_r($replace);
		
		echo str_replace ($search, $replace, $this->text);
	}
}