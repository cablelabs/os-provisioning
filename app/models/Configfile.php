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
            'name' => 'required|unique:configfile,name,'.$id,
            'text' => 'docsis'
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['name', 'text', 'device', 'type', 'parent_id', 'public'];


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
     * Returns the data array that has to be transfered to all views of the model
     */
    public function html_list_array ()
    {
        $ret = array (
                'parents' => $this->parents_list()
            );
        return $ret;
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
	private function __text_make ($m)
	{
		if (!$m)
			return false;
		
		/*
		 * all objects must be an array like a[xyz] = object
		 *
		 * INFO:
		 * - variable names _must_ match tables_a[xyz] coloumn 
		 * - if modem sql relations are not valid a warning will
		 *   be printed
		 */
		$modem  = array ($m);
		$qos    = array ($m->qos);

		/*
		 * generate Table array with SQL columns
		 */
		$tables_a ['modem'][0] = Schema::getColumnListing('modem');
		$tables_a ['qos'][0]   = Schema::getColumnListing('qos');		


		/*
		 * Generate search and replace array
		 */
		$replace = array();

		$i = 0;
		foreach ($tables_a as $name => $tables) 
		{
			foreach ($tables as $j => $table)
			{
				if (isset(${$name}[$j]->id))
				{	
					$replace_a = DB::select ("SELECT * FROM ".$name." WHERE id = ?", array(${$name}[$j]->id))[0];

					foreach ($table as $entry)
					{
						$search[$i]  = '{'.$name.'.'.$entry.'.'.$j.'}';
						$replace[$i] = $replace_a->{$entry};
						
						$i++;
					}
				} 
				else
					Log::warning ('modem cm-'.$m->id.' has no valid '.$name.' entry');
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
			$t .= $p->__text_make($m);
			$p  = $p->get_parent();
		} while ($p);

		return $t;
	}

}