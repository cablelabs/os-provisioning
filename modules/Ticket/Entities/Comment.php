<?php 

namespace Modules\Ticket\Entities;

class Comment extends \BaseModel {

	protected $table = 'comment';

    protected $fillable = [];

    public static function view_headline()
	{
		return 'Comments';
	}

	public static function view_icon()
	{
		return '<i class="fa fa-commenting-o"></i>';
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
				$this->comment, 
			],
//			'index_header' => ['Kommentar'],
			'header' => '(' . $this->id . ') ' . $this->comment
		];
	}

	public function view_belongs_to ()
	{
		return $this->ticket;
	}

	/**
	 * Relations
	 */
	public function ticket()
	{
		return $this->belongsTo('Modules\Ticket\Entities\Ticket', 'ticket_id');
	}
}