<?php

namespace Modules\HfcCustomer\Http\Controllers;

use Modules\HfcCustomer\Entities\ModemHelper;
use Modules\HfcBase\Http\Controllers\TreeController;

use Modules\HfcBase\Entities\Tree;
use Modules\ProvBase\Entities\Modem;

/*
 * Tree Erd (Entity Relation Diagram) Controller
 *
 * One Object represents one SVG Graph
 *
 * @author: Torsten Schmidt
 */
class CustomerTopoController extends TreeController {

	/*
	 * Local tmp folder required for generating the images
	 * app/storage/modules
	 */
	private $path_rel = '/modules/Hfccustomer/kml/';

	// the absolute path: public_path().$this->path_rel
	private $path;

	// filename, will be based on a random hash function
	private $filename;

	/*
	 * The Html Link Target
	 * TODO: make or use a global var ore define
	 */
	private $html_target = '';


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


	#
	# MAIN FUNC
	# exists during historical requirement of multpi Prov Support (should be depracted)
	#
	private function kml_generate($modems)
	{
		# Start KML File
		$file  = $this->file_pre;
		$file .= $this->_kml_generate($modems);
		$file .= $this->file_end;

		# Write Files ..
		$handler = fOpen($this->file, "w");
		fWrite($handler , $file);
		fClose($handler);
	}

		
	#
	# SUB
	#
	private function _kml_generate($modems)
	{
		$x = 0;
		$y = 0;
		$num = 0;
		$hf    = '';
		$str   = '';
		$descr = '';
		$file  = '';


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
			$descr .= "<a target=\"".$this->html_target."\" href='../../Modem/$mid/edit'>$mac</a>, $vertragsnr, $nachname, $hf<br>";
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


		return $file;
		
	}

}

