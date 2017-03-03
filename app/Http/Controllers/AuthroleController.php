<?php

namespace App\Http\Controllers;

use App\Authrole;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\App;

class AuthroleController extends BaseController
{
	protected $index_create_allowed = true;

	public function view_form_fields($model = null)
	{
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Role name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Role type', 'value' => Authrole::getPossibleEnumValues('type', false)),
			array('form_type' => 'text', 'name' => 'description', 'description' => 'Description'),
		);
	}
}
