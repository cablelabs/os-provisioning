<?php

namespace App;
use Silber\Bouncer\Database\Concerns\IsRole;

class Role extends BaseModel
{
	use IsRole;

	public $table = 'roles';
	protected static $undeletables = ['admin', 'support'];

	public static function rules($id=null)
	{
		return array(
			'name' => 'required|unique:roles,name,'.$id.',id,deleted_at,NULL',
		);
	}


	public static function view_headline()
	{
		return 'Roles';
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'title',
		'description',
	];

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-user-circle text-info"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		return [
			'table'			=> $this->table,
			'index_header'	=> [$this->table . '.name'],
			'header'		=> $this->name,
			'order_by'		=> ['0' => 'desc'],
		];
	}


	public function set_index_delete()
	{
		if ( in_array($this->name, self::$undeletables)) {
				$this->index_delete_disabled = true;
			}
	}

	public function view_has_many()
	{
		$ret['Base']['Permissions']['view']['view'] = 'auth.permissions';
		return $ret;
	}


}
