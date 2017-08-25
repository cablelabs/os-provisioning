<?php

BaseRoute::group([], function() {

	// Core Routes
	BaseRoute::resource('Ticket', 'Modules\Ticket\Http\Controllers\TicketController');
	BaseRoute::resource('Comment', 'Modules\Ticket\Http\Controllers\CommentController');
	BaseRoute::resource('Assignee', 'Modules\Ticket\Http\Controllers\AssigneeController');
});