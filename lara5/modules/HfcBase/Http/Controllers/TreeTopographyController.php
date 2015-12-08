<?php

namespace Modules\HfcBase\Http\Controllers;

use Modules\HfcBase\Entities\Tree;

use Acme\php\ArrayHelper;


/*
 * Tree Topography Controller
 *
 * One Object represents one Topography View KML File
 *
 * @author: Torsten Schmidt
 */
class TreeTopographyController extends HfcBaseController {

	/*
	 * Local tmp folder required for generating the kml files
	 * app/storage/modules
	 */
	private $path_rel = '/modules/hfcbase/kml/';


	/*
	 * Constructor: Set local vars
	 */
	public function __construct()
	{ 
		$this->filename = sha1(uniqid(mt_rand(), true)).'kml';	// the filename based on a random hash
		$this->path     = public_path().$this->path_rel;		// absolute path
		$this->file     = $this->path.'/'.$this->filename;		// absolute path of file
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
		$file = $this->kml_generate (Tree::whereRaw($s));

		// Prepare and Topography Map
		$target = $this->html_target;

		$route_name  = 'Tree';
		$view_header = "Topography";
		$body_onload = 'init_for_map';

		$panel_right = [['name' => 'Entity Diagram', 'route' => 'TreeErd.show', 'link' => [$field, $search]], 
						['name' => 'Topography', 'route' => 'TreeTopo.show', 'link' => [$field, $search]]];

		return \View::make('hfcbase::Tree.topo', $this->compact_prep_view(compact('file', 'target', 'route_name', 'view_header', 'panel_right', 'body_onload', 'field', 'search')));
	}


	/*
	 * Generate the KML File
	 *
	 * @param _trees: The Tree Objects to be displayed, without ->get() call
	 * @return the path of the generated *.kml file, could be included via asset ()
	 *
	 * @author: Torsten Schmidt
	 */
	public function kml_generate($_trees)
	{
		$file = $this->file_pre(asset('modules/hfcbase/kml'));

		#
		# Note: OpenLayer draws kml file in parse order, 
		# this requires to build kml files in the following order:
		#  a) Lines
		#  b) Customer Lines
		#  c) Customer Bubbles
		#  d) Pos Elements (Amps, Nodes ..)

		#
		# Draw: Parent - Child - Relationship
		#
		$trees = $_trees->orderBy('pos')->get();
		foreach ($trees as $tree) 
		{
			$pos1   = $tree->pos;	
			$pos2   = $tree->get_parent()->pos;
			$name   = $tree->id;
			$parent = $tree->parent;
			$type   = $tree->type;
			$tp     = $tree->tp;

			# skip empty pos and lines to elements not in search string
			if ($pos2 == null || 
				$pos2 == '' || 
				$pos2 == '0,0' || 
				!ArrayHelper::objArraySearch($trees, 'id', $tree->get_parent()->id))
					continue;

			# Line Color - Style
			$style = '#BLACKLINE';
			if ($type == 'AMP' || $tp == 'FOSTRA')
				$style = '#REDLINE';

			if ($type == 'NODE')
				$style = '#BLUELINE';

			# Draw Line
			$file .= "

			<Placemark>
				<name>$parent -> $name</name>
				<description><![CDATA[]]></description>
				<styleUrl>$style</styleUrl>
				<LineString>
					<tessellate>1</tessellate>
					<coordinates>
						$pos1,0.000000
						$pos2,0.000000
					</coordinates>
				</LineString>
			</Placemark>";
		}


		#
		# Customer
		#
		if ($tree->module_is_active ('HfcCustomer'))
		{
			$modem_helper = 'Modules\HfcCustomer\Entities\ModemHelper';

			$n = 0;
			foreach ($trees as $tree) 
			{
				$id       = $tree->id;
				$name     = $tree->name;
				$pos_tree = $tree->pos;

				$pos = $modem_helper::ms_avg_pos('tree_id='.$tree->id);

				if ($pos['x'])
				{			
					$xavg = $pos['x'];
					$yavg = $pos['y'];			
					$icon = $modem_helper::ms_state_to_color($modem_helper::ms_state ("tree_id = $id"));		
					$icon .= '-CUS';

					# Draw Line - Customer - Amp
					$file .= "

					<Placemark>
						<name></name>
						<description><![CDATA[]]></description>
						<styleUrl>#BLACKLINE2</styleUrl>
						<LineString>
							<tessellate>1</tessellate>
							<coordinates>
								$xavg,$yavg,0.000000
								$pos_tree,0.000000
							</coordinates>
						</LineString>
					</Placemark>";

					# Draw Customer Marker
					$file .= 
					"
					<Placemark>
						<name></name>
						<description><![CDATA[";

							$num  = $modem_helper::ms_num("tree_id = $id");
							$numa = $modem_helper::ms_num_all("tree_id = $id");
							$pro  = round(100 * $num / $numa,0);
							$cri  = $modem_helper::ms_cri("tree_id = $id");
							$avg  = $modem_helper::ms_avg("tree_id = $id");
							$url  = \Request::root()."/Customer/tree_id/$id";

							$file .= "Amp/Node: $name<br><br>Number All CM: $numa<br>Number Online CM: $num ($pro %)<br>Number Critical CM: $cri<br>US Level Average: $avg<br><br><a href=\"$url\" target=\"".$this->html_target."\" alt=\"\">Show all Customers</a>";

							$file .= "]]></description>
							<styleUrl>#$icon</styleUrl>
							<Point>
								<coordinates>$xavg,$yavg,0.000000</coordinates>
							</Point>
						</Placemark>";
				}
			}
		}


		#
		# Fetch unique Geo Positions ..
		#
		$p1 = '';

		foreach ($trees as $tree) 
		{
			$p2  = $tree->pos;
			
			if ($p1 != $p2)
			{
				$rstate  = 0;
				$ystate  = 0;
				$router  = 0;
				$fiber   = 0;

				$file .= "
					<Placemark>
					<name></name>
					<description><![CDATA[";
			}

			$type  = $tree->type;
			$parent= $tree->get_parent()->id;

			if ($tree->state == 'YELLOW')
				$ystate += 1;

			if ($tree->state == 'RED')
				$rstate += 1;

			if (($type == 'CMTS') || ($type == 'CLUSTER') || ($type == 'DATA') || ($type == 'NET'))
				$router += 1;

			if ($type == 'NODE')
				$fiber += 1;

			if ($p1 != $p2) 
			{

				$icon = 'OK';
				if ($ystate)
					$icon = 'YELLOW';
				if ($rstate)
					$icon = 'RED';

				if ($router)
					$icon .= '-ROUTER';
				else if ($fiber)
					$icon .= '-FIB';
				else if ($parent == 1)
					$icon = 'blue-CUS';
			
				$file .= "$p2";
				$file .= "]]></description>
				<styleUrl>#$icon</styleUrl>
				<Point>
					<coordinates>$p2,0.000000</coordinates>
				</Point>
				</Placemark>";
			}

			$p1 = $p2;
		}


		#
		# Write KML File ..
		# 
		$file .= $this->file_post;
		$handler = fOpen($this->file, "w");
		fWrite($handler , $file);
		fClose($handler); // Datei schließen

		return $this->path_rel.'/'.$this->filename;
	}


	private $file_post = "

			</Document>
		</kml>";


	private function file_pre ($p) 
	{
		return "

		<kml xmlns=\"http://earth.google.com/kml/2.2\">
		<Document>
			<name>mbg - amplifier</name>

			<Style id=\"OK\">
				<IconStyle>
					<Icon>
						<href>$p/dot/green-amp.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"YELLOW\">
				<IconStyle>
					<Icon>
						<href>$p/dot/yellow-amp.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"RED\">
				<IconStyle>
					<Icon>
						<href>$p/dot/red-amp.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"OK-FIB\">
				<IconStyle>
					<Icon>
						<href>$p/dot/green-fib.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"YELLOW-FIB\">
				<IconStyle>
					<Icon>
						<href>$p/dot/yellow-fib.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"RED-FIB\">
				<IconStyle>
					<Icon>
						<href>$p/dot/red-fib.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"OK-ROUTER\">
				<IconStyle>
					<Icon>
						<href>$p/dot/router.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"RED-ROUTER\">
				<IconStyle>
					<Icon>
						<href>$p/dot/router-red.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"YELLOW-ROUTER\">
				<IconStyle>
					<Icon>
						<href>$p/dot/router-yellow.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id='green-CUS'>
				<IconStyle>
					<Icon>
						<href>$p/dot/green-dot.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id='yellow-CUS'>
				<IconStyle>
					<Icon>
						<href>$p/dot/yellow-dot.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id='red-CUS'>
				<IconStyle>
					<Icon>
						<href>$p/dot/red-dot.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id='blue-CUS'>
				<IconStyle>
					<Icon>
						<href>$p/dot/blue-dot.png</href>
					</Icon>
				</IconStyle>
			</Style>

			<Style id=\"BLUELINE\">
				<LineStyle>
					<color>FFFF0000</color>
					<width>2</width>
				</LineStyle>
			</Style>

			<Style id=\"REDLINE\">
				<LineStyle>
					<color>FF0000FF</color>
					<width>2</width>
				</LineStyle>
			</Style>

			<Style id=\"BLACKLINE\">
				<LineStyle>
					<color>AA000000</color>
					<width>2</width>
				</LineStyle>
			</Style>

			<Style id=\"BLACKLINE2\">
				<LineStyle>
					<color>AA000000</color>
					<width>1</width>
				</LineStyle>
			</Style>

			";
	}
}