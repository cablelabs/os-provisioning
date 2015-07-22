<?php

use Models\Endpoint;
use Models\Modem;

class EndpointsController extends \BaseController {

	/**
	 * Make Checkbox Default Input
	 * see: see http://forumsarchive.laravel.io/viewtopic.php?id=11627
	 */
	private function default_input ($data)
	{
		/* depracted:
		if(!isset($data['public']))$data['public']=0;
		*/

		return $data;
	}

	/**
	 * Display a listing of endpoints
	 *
	 * @return Response
	 */
	public function index()
	{
		$endpoints = Endpoint::all();

		return View::make('endpoints.index', compact('endpoints'));
	}

	/**
	 * Show the form for creating a new endpoint
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('endpoints.create');
	}

	/**
	 * Store a newly created endpoint in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = $this->default_input(Input::all()), Endpoint::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Endpoint::create($data)->id;

		return Redirect::route('endpoint.edit', $id);
	}

	/**
	 * Display the specified endpoint.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$endpoint = Endpoint::findOrFail($id);

		return View::make('endpoints.show', compact('endpoint'));
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

		return View::make('endpoints.edit', compact('endpoint'));
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

		$validator = Validator::make($data = $this->default_input(Input::all()), Endpoint::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$endpoint->update($data);

		return Redirect::route('endpoint.edit', $id);
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
