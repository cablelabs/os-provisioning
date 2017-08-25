<?php 

namespace Modules\Ticket\Entities;

class Assignee extends \BaseModel {

	protected $table = 'ticketassignee';

	public static function view_headline()
	{
		return 'Assignees';
	}

	public static function view_icon()
	{
		return '<i class="fa fa-address-book-o"></i>';
	}

	public function view_index_label()
	{
		return [
//			'index' => [$this->comment],
//			'index_header' => ['Kommentar'],
			'header' => self::getUserName($this->user_id)
		];
	}

	public function view_belongs_to ()
	{
		return $this->ticket;
	}

	public function ticket()
	{
		return $this->belongsTo('Modules\Ticket\Entities\Ticket', 'ticket_id');
	}

	private static function getUserName($id)
	{
		$user = \App\Authuser::find($id);
		return $user->first_name . ' ' . $user->last_name;
	}
}