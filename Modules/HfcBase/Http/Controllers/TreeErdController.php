<?php

namespace Modules\HfcBase\Http\Controllers;

use Modules\HfcCustomer\Entities\ModemHelper;
use Modules\HfcBase\Entities\Tree;

/*
 * Tree Erd (Entity Relation Diagram) Controller
 *
 * One Object represents one SVG Graph
 *
 * @author: Torsten Schmidt
 */
class TreeErdController extends TreeController {

	/*
	 * Local tmp folder required for generating the images
	 * app/storage/modules
	 */
	private $path_rel = '/modules/Hfcbase/erd/';

	// the absolute path: public_path().$this->path_rel
	private $path;

	// filename, will be based on a random hash function
	private $filename;

	// graph id used for graphviz (svg) naming and html map
	private $graph_id;

	// SVG image size setting
	private $graph_size = '(*,*)';

	/*
	 * The Html Link Target
	 * TODO: make or use a global var ore define
	 */
	private $html_target = '';
	


	/*
	 * Search if $value is in $array field $index
	 *
	 * @param: array: array to search
	 * @param: array: the array[].index field to search in
	 * @param: array: search pattern
	 * @return: the found element, otherwise null
	 *
	 * @author: Torsten Schmidt
	 *
	 * TODO: move to a own Array Helpers Class
	 */
    private static function objArraySearch($array, $index, $value)
    {
        foreach($array as $arrayInf) {
            if($arrayInf->{$index} == $value) {
                return $arrayInf;
            }
        }
        return null;
    }
    

    /*
     * check if $s is a valid geoposition
     */
    private function _is_valid_geopos ($s)
    {
    	$validator = \Validator::make(['a' => "$s"], ['a' => 'geopos']);

		return !$validator->fails();
    }


    /*
     * Constructor: Set local vars
     */
    public function __construct()
    {
    	$this->graph_id = rand(0, 1000000);
		$this->filename = sha1(uniqid(mt_rand(), true));
		$this->path = public_path().$this->path_rel;
    }


	/*
	 * Show Cluster or Network Entity Relation Diagram
	 *
	 * @param field: search field name in tree table
	 * @param search: the search value to look in tree table $field
	 * @return view with SVG image
	 *
	 * @author: Torsten Schmidt
	 */
	public function show($field, $search)
	{
		// prepare search
		$s = "$field='$search'";
		if($field == 'all')
			$s = 'id>2';

		// Generate SVG file 
		$this->graph_generate ($s);

		// Prepare and display SVG
		$is_pos = $this->_is_valid_geopos($search);
		$gid    = $this->graph_id;
		$target = $this->html_target;
		$file   = $this->path_rel.'/'.$this->filename;
		$usemap = str_replace ('alt', 'onContextMenu="return getEl(this.id)" alt', file_get_contents(asset($file.'.map'))); 

		$view_header = "Entity Relation Diagram";
		$route_name  = 'Tree';

		$panel_right = [['name' => 'Entity Diagram', 'route' => 'TreeErd.show', 'link' => [$field, $search]], 
						['name' => 'Topography', 'route' => 'TreeTopo.show', 'link' => [$field, $search]]];

		return \View::make('hfcbase::Tree.erd', $this->compact_prep_view(compact('route_name', 'file', 'target', 'is_pos', 'gid', 'usemap', 'search', 'view_header', 'panel_right', 'view_var', 'field')));
	}


	/*
	 * Generate the SVG and HTML Map File
	 *
	 * @param s: sql search string
	 * @param search: the search value to look in tree table $field
	 * @return view with SVG image
	 *
	 * @author: Torsten Schmidt
	 */
	protected function graph_generate($s, $layer='')
	{

		#
		# INIT
		#
		$gid = $this->graph_id;
		$s   = "(id>2) AND ($s)";

		$file = "digraph tree$gid {

	size=\"$this->graph_size\"


	{
		";

		#
		# Node
		#
		$n  = 0;
		$p1 = '';

		$trees = Tree::whereRaw($s)->orderBy('pos')->get();

		foreach ($trees as $tree) 
		{
			$id = $tree->id;
			$name = $tree->name.' - '.$tree->id;
			$type = $tree->type;
			$state = $tree->state;
			$ip   = $tree->ip;
			$p2   = $tree->pos;
			$parent = $tree->get_parent();
			$n++;

			if ($p1 != $p2)
				$file .= "\n}\nsubgraph cluster_$n {\n style=filled;color=lightgrey;fillcolor=lightgrey;";

			if ($tree->link == '')
				$url  = 'http://'.$tree->ip;
			else
				$url  = $tree->link;
			
			#
			# Amplifier
			#
			if ($ip == '')
				$color = 'green';
			if ($state == 'OK' || $state == '')
				$color = 'green';
			if ($state == 'YELLOW')
				$color = 'yellow';
			if ($state == 'RED')
				$color = 'red';
			if ($state == 'BLUE')
				$color = 'blue';
			
			if ($parent == NULL || $parent->id == 1)
				$file .= "\n node [id = \"$id\" label = \"$id - $name\", shape = rectangle, style = filled, fillcolor=blue, color=darkgrey, URL=\"$url\", target=\"".$this->html_target."\"];";
			else
			{
				if ($type == 'NET')
					$file .= "\n node [id = \"$id\" label = \"$name\", shape = Mdiamond, style = filled, fillcolor=lightblue, color=black URL=\"$url\", target=\"".$this->html_target."\"];";
				else if ($type == 'CLUSTER')
					$file .= "\n node [id = \"$id\" label = \"$name\", shape = Mdiamond, style = filled, fillcolor=white, color=$color, URL=\"$url\", target=\"".$this->html_target."\"];";
				else if ($type == 'CMTS')
					$file .= "\n node [id = \"$id\" label = \"CMTS\\n$name\", shape = hexagon, style = filled, fillcolor=grey, color=$color, URL=\"$url\", target=\"".$this->html_target."\"];";
				else if ($type == 'DATA')
					$file .= "\n node [id = \"$id\" label = \"$name\", shape = rectangle, style = filled, fillcolor=$color, color=darkgrey, URL=\"$url\", target=\"".$this->html_target."\"];";
				else
					$file .= "\n node [id = \"$id\" label = \"$name\", shape = rectangle, style = filled, fillcolor=$color, color=$color, URL=\"$url\", target=\"".$this->html_target."\"];";
			}

			$file .= " \"$id\"";

			$p1 = $p2;
		}
		$file .= "\n}";


		$file .= "\n\n node [shape = diamond];"; 
		#
		# Parent - Child Relations
		#
		foreach ($trees as $tree) 
		{
			$parent = $tree->get_parent()->id;
			$type = $tree->type;
			$tp   = $tree->tp;
			$color = 'black';
			$style = "style=bold";
			if ($type == 'NODE')
			{ 
				$color = 'blue';
				$style='';
			}
			if ($type == 'AMP' || $type == 'CLUSTER' || $tp == 'FOSTRA') 
			{
				$color = 'red';
				$style='';
			}

			if ($parent > 2 && $this->objArraySearch($trees, 'id', $parent))
				$file .= "\n  \"$parent\" -> \"$tree->id\" [color = $color,$style]";

		}

	    #
	    # TODO: Customer
	    #
	if ($tree->module_is_active ('HfcCustomer'))
	{
	    $n = 0;
		foreach ($trees as $tree) 
		{
	        $idtree = $tree->id;
	        $id = $tree->id;
	        $type = $tree->type;
			$url  = "../../../Customer/tree_id/$idtree";
	        $n++;

			$state = ModemHelper::ms_state ("tree_id = $idtree");
			if ($state != -1)
			{
				$color = ModemHelper::ms_state_to_color ($state);
				$num   = ModemHelper::ms_num("tree_id = $idtree");
				$numa  = ModemHelper::ms_num_all("tree_id = $idtree");
				$cri   = ModemHelper::ms_cri("tree_id = $idtree");
				$avg   = ModemHelper::ms_avg("tree_id = $idtree");

				$file .= "\n node [label = \"$numa\\n$num/$cri\\n$avg\", shape = circle, style = filled, color=$color, URL=\"$url\", target=\"".$this->html_target."\"];";
				$file .= " \"C$idtree\"";

				$file .= "\n \"$id\" -> C$idtree [color = green]";
			}
		}
	}


		$date = date('l jS \of F Y H:i:s A');
		$file .= "\nlabel = \" - Entity Relation Diagram - \\n$date\";\n fontsize=20;";
		$file .= "
		}
		";

		#
		# Write Files ..
		#
		$path = $this->path; # use relative path directory
		$handler = fOpen($path.'/'.$this->filename.'.dot', "w");
		fWrite($handler , $file);
		fClose($handler); // Datei schließen

		exec (" dot -v -Tcmapx -o $path/$this->filename.map -Tsvg -o $path/$this->filename.svg $path/$this->filename.dot 1>$path/$this->filename.log 2>&1 && 
				echo \"<IMG SRC=\"$this->filename.svg\" USEMAP=#tree$gid />\" > $path/$this->filename.html && 
				cat $path/$this->filename.map | sed 's/alt/onContextMenu\=\"return\ getEl\(this.id\)\"\ alt/g' >> $path/$this->filename.html");
	}

}
