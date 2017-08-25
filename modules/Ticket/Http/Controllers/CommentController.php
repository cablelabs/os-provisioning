<?php

namespace Modules\Ticket\Http\Controllers;

class CommentController extends \BaseController {
	
	protected $index_delete_allowed = false;

	public function view_form_fields($model = null)
	{
		if (!$model) {
			$model = new Comment;
		}

		return array(
			array(
				'form_type' => 'textarea', 
				'name' => 'comment', 
				'description' => 'Comment'
			),
			array(
				'form_type' => 'text', 
				'name' => 'ticket_id', 
				'hidden' => 1,
				'value' => 1
			),
			array(
				'form_type' => 'text', 
				'name' => 'user_id', 
				'hidden' => 1, 
				'init_value' => \Auth::user()->id
			),
		);
	}
}