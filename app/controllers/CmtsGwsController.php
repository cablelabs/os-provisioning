<?php

class CmtsGwsController extends \BaseController {

	/**
	 * Display a listing of cmtsgws
	 *
	 * @return Response
	 */
	public function index()
	{
		$cmtsgws = CmtsGw::all();

		return View::make('cmtsgws.index', compact('cmtsgws'));
	}

	/**
	 * Show the form for creating a new cmtsgw
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('cmtsgws.create');
	}

	/**
	 * Store a newly created cmtsgw in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Cmtsgw::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Cmtsgw::create($data);

		return Redirect::route('cmtsgws.index');
	}

	/**
	 * Display the specified cmtsgw.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cmtsgw = Cmtsgw::findOrFail($id);

		return View::make('cmtsgws.show', compact('cmtsgw'));
	}

	/**
	 * Show the form for editing the specified cmtsgw.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$cmtsgw = Cmtsgw::find($id);

		return View::make('cmtsgws.edit', compact('cmtsgw'));
	}

	/**
	 * Update the specified cmtsgw in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$cmtsgw = Cmtsgw::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Cmtsgw::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$cmtsgw->update($data);

		return Redirect::route('cmtsgws.index');
	}

	/**
	 * Remove the specified cmtsgw from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Cmtsgw::destroy($id);

		return Redirect::route('cmtsgws.index');
	}

}
