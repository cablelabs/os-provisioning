<?php

namespace Modules\HfcBase\Entities;

class Tree extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'tree';

	// Don't forget to fill this array
	protected $fillable = ['name', 'type', 'ip', 'pos', 'link', 'state', 'options', 'descr', 'parent', 'access', 'net', 'cluster', 'layer', 'kml_file'];


    public $kml_path = '/var/www/lara/app/storage/hfc/kml/static/';

	// Add your validation rules here
    public static function rules($id = null)
    {
        return array(
            'name' => 'required|string',
            'ip' => 'ip',
            'pos' => 'geopos'
        );
    }
    
    // Name of View
    public static function get_view_header()
    {
        return 'Tree Table';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->id.' : '.$this->type.' : '.$this->name.' '.$this->state;
    }	

    /**
     * TODO: make one function
     * returns a list of possible parent configfiles
     * Nearly the same like html_list method of BaseModel but needs zero element in front
     */
    public function parents_list ()
    {
        $parents = array('0' => 'Null');
        foreach (Tree::all() as $p)
        {
            if ($p->id != $this->id)
                $parents[$p->id] = $p->name;
        }
        return $parents;
    }

    public function parents_list_all ()
    {
        $parents = array('0' => 'Null');
        foreach (Tree::all() as $p)
        {
            $parents[$p->id] = $p->name;
        }
        return $parents;
    }

    public function get_parent ()
    {
        return Configfile::find($this->parent);
    }

    /**
     * Returns all available firmware files (via directory listing)
     * @author Patrick Reichel
     */
    public function kml_files() 
    {
        // get all available files
        $kml_files_raw = glob($this->kml_path.'/*');
        $kml_files = array(null => "None");
        // extract filename
        foreach ($kml_files_raw as $file) {
            if (is_file($file)) {
                $parts = explode("/", $file);
                $filename = array_pop($parts);
                $kml_files[$filename] = $filename;
            }
        }
        return $kml_files;
    }

}
