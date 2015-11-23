<?php

namespace Modules\HfcBase\Entities;

class Tree extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'tree';

	// Don't forget to fill this array
	protected $fillable = ['name', 'type', 'ip', 'pos', 'link', 'state', 'options', 'descr', 'parent', 'access', 'net', 'cluster', 'layer', 'kml_file'];


	public $kml_path = '/var/www/lara/app/storage/hfc/kml/static/';
    private $max_parents = 25;

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
		return $this->id.' : '.$this->type.' : '.$this->name.' '.$this->state.' - '.$this->get_native_fibre();
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
        if (!isset($this->parent) || $this->parent < 1)
            return 0;

		return Tree::find($this->parent);
	}


    // TODO: rename, avoid recursion
    public function get_non_location_parent($layer='')
    {   
        return $this->get_parent();


        $p = $this->get_parent();

        if ($p->type == 'LOCATION')
            return get_non_location_parent($p);
        else
            return $p;
    }

    public function get_children ()
    {
        return Tree::whereRaw('parent = '.$this->id)->get();
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


    /*
     * Helpers from NMS
     */
	private function _get_native_helper ($type = 'NET')
    {
		$p = $this;
		$i = 0;

		do
		{
            

            if (!is_object($p))
                return 0;

			if ($p->type == $type)
				return $p->id;

            $p = $p->get_parent();
		} while ($i++ < $this->max_parents);
    }

    public function get_native_fibre ()
    {
        return $this->_get_native_helper('CLUSTER');
    }

    public function get_native_net ()
    {
        return $this->_get_native_helper('NET');
    }

    // TODO: depracted, remove
    public function get_layer_level($layer='')
    {
		return 0;
    }


    /**
     * BOOT:
     * - init tree observer
     */
    public static function boot()
    {
        parent::boot();

        Tree::observe(new TreeObserver);
    }


    public function build ()
    {
        $tree->net   = $tree->get_native_net();
        $tree->cluster = $tree->get_native_fibre();
    }
}


/**
 * Tree Observer Class
 * Handles changes on CMs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class TreeObserver
{


    public function created($tree)
    {  
        //$tree->build_all();
        $tree->save();
    }

    public function updating($tree)
    {
        //$tree->build_all();
    }

    public function deleted($tree)
    {
    }
}