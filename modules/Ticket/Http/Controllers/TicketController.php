<?php namespace Modules\Ticket\Http\Controllers;

use Modules\Ticket\Entities\Ticket;

class TicketController extends \BaseController {
	
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Ticket;

		return array(
			array(
				'form_type' => 'text', 
				'name' => 'name', 
				'description' => 'Ticket title'
			),
			array(
				'form_type' => 'select', 
				'name' => 'type', 
				'description' => 'Ticket type', 
				'value' => self::getSelectBoxValues('types')
			),
			array(
				'form_type' => 'select', 
				'name' => 'state', 
				'description' => 'Ticket state', 
				'value' => self::getSelectBoxValues('states')
			),
			array(
				'form_type' => 'select', 
				'name' => 'priority', 
				'description' => 'Ticket priority', 
				'value' => self::getSelectBoxValues('priorities')
			),
			array(
				'form_type' => 'textarea', 
				'name' => 'description', 
				'description' => 'Ticket description'
			),
			array(
				'form_type' => 'text', 
				'name' => 'user_id', 
				'description' => 'Current user', 
				'init_value' => \Auth::user()->id,
				'hidden' => 1
			),
		);
	}

	private static function getSelectBoxValues($scope)
	{
		$ret = [];
		$configData = \Config::get('ticket.' . $scope);

		switch ($scope) {
			case 'types':
				$trans = 'Ticket_Type';
				break;

			case 'priorities':
				$trans = 'Ticket_Priority';
				break;

			case 'states':
				$trans = 'Ticket_State';
				break;
		}

		foreach ($configData as $key => $value) {
			$ret[$key] =  \App\Http\Controllers\BaseViewController::translate_view($value, $trans);
		}

		return $ret;
	} 	
}