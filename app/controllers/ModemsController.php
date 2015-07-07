<?php

class ModemsController extends \BaseController {

	/**
	 * Display a listing of modems
	 *
	 * @return Response
	 */
	public function index()
	{
		$modems = Modem::all();

		return View::make('modems.index', compact('modems'));
	}

	/**
	 * Show the form for creating a new modem
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('modems.create');
	}

	/**
	 * Store a newly created modem in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Modem::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Modem::create($data)->id;

		return Redirect::route('modem.edit', $id);
	}

	/**
	 * Display the specified modem.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$modem = Modem::findOrFail($id);

		return View::make('modems.show', compact('modem'));
	}

	/**
	 * Show the form for editing the specified modem.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$modem = Modem::find($id);

		return View::make('modems.edit', compact('modem'));
	}

	/**
	 * Update the specified modem in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$modem = Modem::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Modem::rules($id));


		if (Input::get('network_access') == '1')
			$modem->network_access = 1;
		else
			$modem->network_access = 0;

		if (Input::get('public') == '1')
			$modem->public = 1;
		else
			$modem->public = 0;
		

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$modem->update($data);

		return Redirect::route('modem.edit', $id);
	}

	/**
	 * Remove the specified modem from storage.
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			foreach (Input::all()['ids'] as $id => $val)
				Modem::destroy($id);
		}
		else
			Modem::destroy($id);

		return Redirect::back();
	}



}
