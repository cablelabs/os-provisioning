<?php

use Models\Endpoint;
use Models\Modem;

class EndpointController extends \BaseController {

	/**
	 * Display a listing of endpoints
	 *
	 * @return Response
	 */
	public function index()
	{
		$endpoints = Endpoint::all();

		return View::make('Endpoint.index', compact('endpoints'));
	}

	/**
	 * Show the form for creating a new endpoint
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Endpoint.create');
	}


	/**
	 * Show the form for editing the specified endpoint.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$endpoint = Endpoint::find($id);

		return View::make('Endpoint.edit', compact('endpoint'));
	}


	/**
	 * Store a newly created endpoint in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make(Input::all(), Endpoint::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Endpoint::create($data)->id;

		return Redirect::route('Endpoint.edit', $id);
	}


	/**
	 * Update the specified endpoint in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$endpoint = Endpoint::findOrFail($id);

		$validator = Validator::make(Input::all(), Endpoint::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$endpoint->update($data);

		return Redirect::route('Endpoint.edit', $id);
	}

	/**
	 * Remove the specified endpoint from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			foreach (Input::all()['ids'] as $id => $val)
				Endpoint::destroy($id);
		}
		else
			Endpoint::destroy($id);

		return $this->index();
	}

}
