<?php

class DeviceTypeController extends \BaseController {

	/**
	 * Display a listing of DeviceType
	 *
	 * @return Response
	 */
	public function index()
	{
		$devicetypes = DeviceType::all();

		return View::make('DeviceType.index', compact('devicetypes'));
	}

	/**
	 * Show the form for creating a new DeviceType
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('DeviceType.create');
	}

	/**
	 * Store a newly created DeviceType in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), DeviceType::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		DeviceType::create($data);

		return Redirect::route('DeviceType.index');
	}


	/**
	 * Show the form for editing the specified DeviceType.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$devicetype = DeviceType::find($id);

		return View::make('DeviceType.edit', compact('devicetype'));
	}

	/**
	 * Update the specified DeviceType in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$devicetype = DeviceType::findOrFail($id);

		$validator = Validator::make($data = Input::all(), DeviceType::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$devicetype->update($data);

		return Redirect::route('DeviceType.index');
	}

	/**
	 * Remove the specified DeviceType from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		DeviceType::destroy($id);

		return Redirect::route('DeviceType.index');
	}

}
