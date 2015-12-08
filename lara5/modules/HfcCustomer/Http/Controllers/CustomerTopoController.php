<?php

namespace Modules\HfcCustomer\Http\Controllers;

use Modules\HfcCustomer\Entities\ModemHelper;
use Modules\HfcBase\Http\Controllers\TreeController;

use Modules\HfcBase\Entities\Tree;
use Modules\ProvBase\Entities\Modem;


/*
 * Show Customers (Modems) on Topography
 *
 * One Object Represents one Topography View - KML File
 *
 * @author: Torsten Schmidt
 */
class CustomerTopoController extends TreeController {

	/*
	 * Local tmp folder required for generating the images
	 * app/storage/modules
	 */
	private $path_rel = '/modules/hfccustomer/kml/';

	// the absolute path: public_path().$this->path_rel
	private $path;

	// filename, will be based on a random hash function
	private $filename;


	/*
	 * File Specific Stuff
	 */
	private $file_pre = "<?xml version='1.0' encoding='UTF-8'?>
		<kml xmlns='http://earth.google.com/kml/2.2'>
		<Document>
		  <name>mbg - Kunden</name>
		  <description><![CDATA[]]></description>
		  
		  
		  <Style id='styleokay'>
			<IconStyle>
			  <Icon>
				<href>http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/green-dot.png</href>
			  </Icon>
			</IconStyle>
		  </Style>
		
		  <Style id='stylecritical'>
			<IconStyle>
			  <Icon>
				<href>http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/yellow-dot.png</href>
			  </Icon>
			</IconStyle>
		  </Style>
		
		  <Style id='styleoffline'>
			<IconStyle>
			  <Icon>
				<href>http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/red-dot.png</href>
			  </Icon>
			</IconStyle>
		  </Style>
		
		  <Style id='styleunknown'>
			<IconStyle>
			  <Icon>
				<href>http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/blue-dot.png</href>
			  </Icon>
			</IconStyle>
		  </Style>

		  <Style id=\"YELLOWLINE\">
		    <LineStyle>
	      		<color>55000000</color>
	     	 <width>1</width>
	    	</LineStyle>
		  </Style>


		";

	private $file_end = "</Document></kml>";


	/*
	 * Constructor: Set local vars
	 */
	public function __construct()
	{ 
		$this->path     = public_path().$this->path_rel;
		$this->filename = sha1(uniqid(mt_rand(), true)).'.kml';
		$this->file     = $this->path.'/'.$this->filename;
	}


	/**
	 * Show Modems matching Modem sql $field = $value
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

		return $this->show_modems(Modem::whereRaw($s), $field, $search);
	}


	/*
	* Show Customer in Rectangle
	*
	* @param field: search field name in tree table
	* @param search: the search value to look in tree table $field
	* @return view with SVG image
	*
	* @author: Torsten Schmidt
	*/
	public function showRect($x1, $x2, $y1, $y2)
	{
		return $this->show_modems(Modem::whereRaw("(($x1 < x) AND (x < $x2) AND ($y1 < y) AND (y < $y2))"));
	}


	/*
	* Show Modems om Topography
	*
	* @param modems the preselected Modem model, like Modem::where()
	* @param field search field name in tree table, only for display
	* @param search the search value to look in tree table $field, only for display
	* @return view with SVG image
	*
	* @author: Torsten Schmidt
	*/
	public function show_modems($modems, $field=null, $search=null)
	{
		// Generate SVG file 
		$file = $this->kml_generate ($modems);

		// Prepare and Topography Map
		$target      = $this->html_target;
		$route_name  = 'Tree';
		$view_header = "Topography - Modems";
		$body_onload = 'init_for_map';

		return \View::make('hfcbase::Tree.topo', $this->compact_prep_view(compact('file', 'target', 'route_name', 'view_header', 'body_onload', 'field', 'search')));
	}



	/**
	 * Generate KML File with Customer Modems Inside
	 *
	 * @param modems the Modem models to display, like Modem::where()
	 * @returns the path of the generated *.kml file to be included via asset ()
	 *
	 * @author: Torsten Schmidt
	 */
	public function kml_generate($modems)
	{
		$x = 0;
		$y = 0;
		$num = 0;
		$hf    = '';
		$str   = '';
		$descr = '';
		$file  = $this->file_pre;

		foreach ($modems->orderByRaw('10000000*x+y')->get() as $modem)
		{
			#
			# Print Marker AND Reset Vars IF new GPS position
			#
			if ($x != $modem->x || $y != $modem->y)
			{
				# Print Marker
				$style = "#style$hf"; # green, yellow, red
				
				# Reset Vars
				$hf = '';
				$pos ="$x, $y, 0.000000";

				
				if ($x)                  # ignore (0,0)
				{
					$descr .= "<br><div align=right><a target=\"".$this->html_target."\" 
						   href=\"../customer/mps.php?mp_sys_operation=mp_op_Add&pos=$pos\">
						   Set New Parent Device</a></div>";
					$file .= "\n <Placemark><name>1</name>
						 <description><![CDATA[$descr]]></description>
						 <styleUrl>$style</styleUrl> 
						 <Point><coordinates>$pos</coordinates></Point></Placemark>";
					$file .= "\n <Placemark><name>$num</name>
						 <Point><coordinates>$pos</coordinates></Point></Placemark>";
				}

				# Reset Var's
				$state = 3;      # unknown
				$descr = '<br>'; # new line for descr
				$x = $modem->x;  # get next GPS pos ..
				$y = $modem->y;
				$num = 0;
			}
		

			# modem
			$mid    = $modem->id;
			$mac    = $modem->mac;
			$status = $modem->status;

			if ($modem->status == 0) 
				$status = 'offline';
			else if ($modem->status < 550)
				$status = 'okay';     
			else
				$status = 'critical';

			# marker status
			if ($modem->status == 0 && $hf != 'critical' && $hf != 'okay') 
				$hf = 'offline';
			else if ($modem->status < 550 && $hf != 'critical')
				$hf = 'okay';     
			else
				$hf = 'critical';

			#
			# TODO: Contract
			#
if (0) 
{
			# contract	
			$contract   = $modem->contract;
			$vertragsnr = $contract->vertragsnummer;
			$nachname   = utf8_encode($contract->nachname);

			# Headline: Address from DB
			if ($str != $modem->strasse || $ort != $modem->ort || $plz != $modem->plz) 
			{
				$str = utf8_encode($modem->strasse);
				$ort = utf8_encode($modem->ort);
				$plz = $modem->plz;
				$descr .= "<b>$plz, $ort, $str</b><br>";
			}
			
}
else
{
			$descr .= "<b>Postcode, Place, Street</b><br>";
			$vertragsnr = 'contract-nr';
			$nachname   = "Lastname";
}

			# add descr line
			$descr .= "<a target=\"".$this->html_target."\" href='".\Request::root()."/Modem/$mid/edit'>$mac</a>, $vertragsnr, $nachname, $hf<br>";
			$num += 1;
		}


		#
		# Print Last Marker
		#
		$style = "#style$hf"; # green, yellow, red
		$pos ="$x, $y, 0.000000";
		if ($x) 
		{
			$descr .= "<br><div align=right><a target=\"".$this->html_target."\" 
				   href=\"../customer/mps.php?mp_sys_operation=mp_op_Add&pos=$pos\">
				   Set New Parent Device</a></div>";
			$file .= "\n <Placemark><name></name>
				 <description><![CDATA[$descr]]></description>
				 <styleUrl>$style</styleUrl> 
				 <Point><coordinates>$pos</coordinates></Point></Placemark>";
			$file .= "\n <Placemark><name>$num</name>
				 <Point><coordinates>$pos</coordinates></Point></Placemark>";
		}


		
		# Write Files ..
		$file .= $this->file_end;
		$handler = fOpen($this->file, "w");
		fWrite($handler , $file);
		fClose($handler);
		
		return $this->path_rel.'/'.$this->filename;
	}

}

