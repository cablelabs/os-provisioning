<?php

namespace Modules\HfcSnmp\Entities;


/**
 * The OID Model with it's OID and Properties from MibFile (access, type, description,...) and html properties for WebGUI View
 *
 * Type can have the following Values
	i 	INTEGER
	u 	unsigned INTEGER
	t 	TIMETICKS
	a 	IPADDRESS
	o 	OBJID
	s 	STRING
	x 	HEX STRING
	d 	DECIMAL STRING
	n 	NULLOBJ
	b 	BITS 
 */
class OID extends \BaseModel {

	public $table = 'oid';

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'oid' => 'required',
        );
    }

	// Name of View
	public static function view_headline()
	{
		return 'OID';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->name_gui, $this->oid, $this->access],
		        'index_header' => ['Name', 'Descriptive Name', 'OID', 'Access'],
		        'header' => $this->name.' - '.$this->oid];
	}

	public function index_list()
	{
		return $this->orderBy('oid')->simplePaginate(1000);
	}


	/**
	 * Relations
	 */
	public function mibfile()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\MibFile');
	}

	// public function netelementtypes()
	// {
	// 	return $this->belongsToMany('Modules\HfcReq\Entities\NetElementType', 'netelementtype_oid', 'oid_id', 'netelementtype_id');
	// }

	public function parameters()
	{
		// NOTE: This should be done with eager loading if not already done by laravel automatically, because oid relation is needed close to all the time
		return $this->HasMany('Modules\HfcSnmp\Entities\Parameter', 'oid_id');
		// ->with('Modules\HfcSnmp\Entities\OID')->get();
	}

	public function view_belongs_to ()
	{
		return $this->mibfile;
	}


	/**
	 * Return The Select Values for the Parameter in the NetElement Controlling View
	 *
	 * @return Array
	 */
	public function get_select_values()
	{
		if ($this->value_set)
		{
			// create list
			$separator_1 = [',', ' ', ';'];
			$separator_2 = ['=', ':'];

			$pairs = str_replace($separator_1, $separator_1[0], $this->value_set);
			$pairs = explode($separator_1[0], $pairs);

			foreach ($pairs as $value)
			{
				$key_value = str_replace($separator_2, $separator_2[0], $value, $cnt);

				if (!$cnt)
				{
					// Mib-format valuename(value)
					$key_value = [];

					$key_value[0] = substr($value, 0, $x = strpos($value, '('));
					$key 		  = substr($value, $x + 1);
					$key_value[1] = substr($key, 0, strlen($key) -1);
				}
				else
					$key_value = explode($separator_2[0], $key_value);

				// discard empty strings caused by spaces
				if ($key_value[0])
					$list[$key_value[1]] = $key_value[0];
			}
// if ($this->name_gui == 'Forward Equalization')
// 	d($list);
			return $list;
		}

		if ($this->endvalue)
		{
			$this->stepsize = $this->stepsize ? : 1;

			for ($i = $this->startvalue; $i <= $this->endvalue; $i += $this->stepsize)
				$list[$i] = $i;

			return $list;
		}

		return [];
	}


	/**
	 * Return SNMP OID Type Character from Syntax String (for OID Type field)
	 *
	 * @return String 	Enum for OIDs SNMP Type
	 */
	public static function get_oid_type($string)
	{
		if (strpos($string, 'unsigned integer') !== false)
			return 'u';
		else if (strpos($string, 'integer') !== false)
			return 'i';
		else if (strpos($string, 'decimal string') !== false)
			return 'd';
		else if (strpos($string, 'hex string') !== false)
			return 'x';
		else if (strpos($string, 'string') !== false)
			return 's';
		else if (strpos($string, 'counter') !== false)
			return 'i';
		else if (strpos($string, 'timeticks') !== false)
			return 't';
		else if (strpos($string, 'ipaddress') !== false)
			return 'a';
		else if (strpos($string, 'bits') !== false)
			return 'b';

		return '';
	}


	/**
	 * Return value set from Syntax field of OID or Start & Endvalue
	 *
	 * @param 	String 			Syntax field
	 * @return 	String|Array 	
	 */
	public static function get_value_set($string)
	{
		if ($x = strpos($string, '{') !== false)
		{
			// value_set
			return substr($string, $x, strlen($string) - 1);
		}


		if ($x = strpos($string, '(') !== false)
		{
			// start & end value
			$y 		  = strpos($string, '..');
			$startval = substr($string, $x, $y - $x);
			$endval   = substr($string, $y + 2, strlen($string) - 1);
			
			return [$startval, $endval];
		}

		return null;
	}

}