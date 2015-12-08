<?php

namespace Modules\HfcBase\Http\Controllers;

use Modules\HfcCustomer\Entities\ModemHelper;
use Modules\HfcBase\Entities\Tree;

use Acme\php\ArrayHelper;

/*
 * Tree Erd (Entity Relation Diagram) Controller
 *
 * One Object represents one SVG Graph
 *
 * @author: Torsten Schmidt
 */
class TreeErdController extends HfcBaseController {

	/*
	 * Local tmp folder required for generating the images
	 * app/storage/modules
	 */
	private $path_rel = '/modules/hfcbase/erd/';

	// graph id used for graphviz (svg) naming and html map
	private $graph_id;

	// SVG image size setting
	private $graph_size = '(*,*)';
    

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

    	// Note: we create several files with differnt endings *.dot, *.svg, *.map
		$this->filename = sha1(uniqid(mt_rand(), true));	// the filename based on a random hash
		$this->path     = public_path().$this->path_rel;	// absolute path
		$this->file     = $this->path.'/'.$this->filename;	// absolute path of file
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
		$file = $this->graph_generate (Tree::whereRaw($s));

		if(!$file)
			return \View::make('error');

		// Prepare and display SVG
		$is_pos = $this->_is_valid_geopos($search);
		$gid    = $this->graph_id;
		$target = $this->html_target;

		$arrContextOptions=array(
  			"ssl"=>array(
		        	"verify_peer"=>false,
			        "verify_peer_name"=>false,
			),
		);
		$usemap = str_replace ('alt', 'onContextMenu="return getEl(this.id)" alt', file_get_contents(asset($file.'.map'), false, stream_context_create($arrContextOptions))); 

		$view_header = "Entity Relation Diagram";
		$route_name  = 'Tree';

		$panel_right = [['name' => 'Entity Diagram', 'route' => 'TreeErd.show', 'link' => [$field, $search]], 
						['name' => 'Topography', 'route' => 'TreeTopo.show', 'link' => [$field, $search]]];

		$preselect_field = $field;
		$preselect_value = $search;

		return \View::make('hfcbase::Tree.erd', $this->compact_prep_view(compact('route_name', 'file', 'target', 'is_pos', 'gid', 'usemap', 'preselect_field', 'view_header', 'panel_right', 'view_var', 'preselect_value')));
	}


	/*
	 * Generate the SVG and HTML Map File
	 *
	 * @param _trees: The Tree Objects to be displayed, without ->get() call
	 * @return the path of the generated file(s) without ending
	 *         this files could be included via asset ()
	 *
	 * @author: Torsten Schmidt
	 */
	public function graph_generate($_trees)
	{

		#
		# INIT
		#
		$gid = $this->graph_id;

		$file = "digraph tree$gid {

	size=\"$this->graph_size\"


	{
		";

		$n  = 0;
		$p1 = '';

		$trees = $_trees->where('id', '>', '2')->orderBy('pos')->get();

		if (!$trees->count())
			return null;

		#
		# Node
		#
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
			$_parent = $tree->get_parent();
			$parent = 0;
			if ($_parent)
				$parent = $_parent->id;

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

			if ($parent > 2 && ArrayHelper::objArraySearch($trees, 'id', $parent))
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
				$url  = \Request::root()."/Customer/tree_id/$idtree";
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
		$file .= "\nlabel = \" - Entity Relation Diagram - \\n$date\";\n fontsize=20;\n\n}";

		#
		# Write Base Files *.dot for SVG translation ..
		#
		$fn = $this->file;
		$handler = fOpen($fn.'.dot', "w");
		fWrite($handler , $file);
		fClose($handler); // Datei schließen

		#
		# Create SVG
		# Debug File: Add o exec: '1>$fn.log 2>&1';
		#
		exec ("dot -v -Tcmapx -o $fn.map -Tsvg -o $fn.svg $fn.dot");

		return $this->path_rel.'/'.$this->filename;
	}

}
