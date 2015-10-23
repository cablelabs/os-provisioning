<?php

class DeviceController extends \BaseController {

	/**
	 * Display a listing of Device
	 *
	 * @return Response
	 */
	public function index()
	{
		$devices = Device::all();

		return View::make('Device.index', compact('devices'));
	}

	/**
	 * Show the form for creating a new Device
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Device.create');
	}

	/**
	 * Store a newly created Device in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Device::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Device::create($data);

		return Redirect::route('Device.index');
	}


	/**
	 * Show the form for editing the specified Device.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$device = Device::find($id);

		return View::make('Device.edit', compact('device'));
	}

	/**
	 * Update the specified Device in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$device = Device::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Device::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$device->update($data);

		$success = 1;

		return View::make('Device.edit', compact('device', 'success'));
	}


}
