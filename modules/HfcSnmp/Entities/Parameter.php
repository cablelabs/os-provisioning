<?php

namespace Modules\HfcSnmp\Entities;

class Parameter extends \BaseModel {

	public $table = 'parameter';

	public $guarded = ['name', 'table'];


	public static function boot()
	{
		parent::boot();

		Parameter::observe(new ParameterObserver);
	}


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'html_frame' => 'numeric|min:1',
			'html_id' => 'numeric|min:0',
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'Parameter';
	}

	// View Icon
	public static function view_icon()
	{
	  return '<i class="fa fa-dot-circle-o"></i>';
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label()
	{
		$header = isset($this->oid) ? $this->oid->name : '' ;
		$header .= isset($this->oid) ? ' - '.$this->oid->oid : '';

		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => ['oid.name', 'oid.oid',  'oid.access'],
				'header' =>  $header,
				'order_by' => ['1' => 'asc'],
				'eager_loading' => ['oid']];
	}

	public function get_bsclass()
	{
		$bsclass = 'success';

		if (isset($this->oid) && $this->oid->access == 'read-only')
			$bsclass = 'danger';

		return $bsclass;
	}

	public function view_has_many()
	{
		$ret = [];

		if ($this->oid->oid_table)
		{
			$ret['Base']['SubOIDs']['view']['view'] = 'hfcreq::NetElementType.parameters';
			$ret['Base']['SubOIDs']['view']['vars']['list'] = $this->children() ? : [];
		}

		return $ret;
	}

	public function view_belongs_to ()
	{
		return $this->netelementtype;
	}


	/**
	 * Relations
	 */
	public function oid()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\OID', 'oid_id');
	}

	public function netelementtype()
	{
		return $this->belongsTo('Modules\HfcReq\Entities\NetElementType', 'netelementtype_id');
	}

	public function indices()
	{
		return $this->hasOne('Modules\HfcSnmp\Entities\Indices');
	}




	public function children()
	{
		return Parameter::where('parent_id', '=', $this->id)->orderBy('third_dimension')->orderBy('html_id')->orderBy('id')->get()->all();
	}

	public function third_dimension_params()
	{
		return Parameter::where('parent_id', '=', $this->id)->where('third_dimension', '=', 1)->orderBy('id')->get()->all();
	}

}


class ParameterObserver {

	public function creating($parameter)
	{
		$parameter->divide_by = str_replace([' ', "\t"], '', $parameter->divide_by);
	}

	public function updating($parameter)
	{
		$parameter->divide_by = str_replace([' ', "\t"], '', $parameter->divide_by);
	}

}
