<?php

namespace Modules\Ticket\Entities;
   
class Ticket extends \BaseModel {

    protected $table = 'ticket';

	public static function view_headline()
	{
		return 'Tickets';
	}

	public static function view_icon()
	{
		return '<i class="fa fa-ticket"></i>';
	}

	public function index_list()
	{
		return $this->orderBy('id', 'desc')->get();
	}

	public function view_index_label()
	{
		return [
			'index' => [
				$this->id, 
				$this->name, 
				self::getDescriptionValues($this->type, 'types'), 
				self::getDescriptionValues($this->priority, 'priorities'), 
				self::getDescriptionValues($this->state, 'states'), 
				self::getUserName($this->user_id), 
				$this->created_at
			],
			'index_header' => ['Ticket Nr', 'Title', 'Type', 'Priority', 'State', 'Created by', 'Created at'],
			'header' => $this->name
		];
	}

	public function view_has_many()
	{
		$ret = array();

		// we use a dummy here as this will be overwritten by ModemController::get_form_tabs()
		// $ret['Edit']['Comment']['class'] = 'Comment';
		// $ret['Edit']['Comment']['relation'] = $this->comments;
		$ret['Edit']['Comment'] = $this->comments;
		$ret['Edit']['Assignee'] = $this->assignees;

		return $ret;
	}	
	
	public function comments()
	{
		return $this->hasMany('Modules\Ticket\Entities\Comment');
	}	
	
	public function assignees()
	{
		return $this->hasMany('Modules\Ticket\Entities\Assignee');
	}

	private static function getDescriptionValues($id, $scope)
	{
		$configData = \Config::get('ticket.' . $scope);

		switch ($scope) {
			case 'types':
				$ret = \App\Http\Controllers\BaseViewController::translate_view($configData[$id], 'Ticket_Type');
				break;

			case 'priorities':
				$ret = \App\Http\Controllers\BaseViewController::translate_view($configData[$id], 'Ticket_Priority');
				break;

			case 'states':
				$ret = \App\Http\Controllers\BaseViewController::translate_view($configData[$id], 'Ticket_State');
				break;
		}

		return $ret;
	}

	private static function getUserName($id)
	{
		$user = \App\Authuser::find($id);
		return $user->first_name . ' ' . $user->last_name;
	}
}