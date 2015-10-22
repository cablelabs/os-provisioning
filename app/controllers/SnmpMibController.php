<?php

class SnmpMibController extends \BaseController {

	/**
	 * Display a listing of snmpmib
	 *
	 * @return Response
	 */
	public function index()
	{
		$snmpmibs = SnmpMib::all();

		return View::make('SnmpMib.index', compact('snmpmibs'));
	}

	/**
	 * Show the form for creating a new snmpmib
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('SnmpMib.create');
	}

	/**
	 * Store a newly created snmpmib in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), SnmpMib::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		SnmpMib::create($data);

		return Redirect::route('SnmpMib.index');
	}

	/**
	 * Display the specified SnmpMib.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$snmpmib = SnmpMib::findOrFail($id);

		return View::make('SnmpMib.show', compact('snmpmib'));
	}

	/**
	 * Show the form for editing the specified SnmpMib.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$snmpmib = SnmpMib::find($id);

		return View::make('SnmpMib.edit', compact('snmpmib'));
	}

	/**
	 * Update the specified snmpmib in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$snmpmib = SnmpMib::findOrFail($id);

		$validator = Validator::make($data = Input::all(), SnmpMib::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$snmpmib->update($data);

		return Redirect::route('SnmpMib.index');
	}

	/**
	 * Remove the specified snmpmib from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		SnmpMib::destroy($id);

		return Redirect::route('SnmpMib.index');
	}

}
