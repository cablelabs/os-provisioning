<?php

class CmtsGwsController extends \BaseController {

	/**
	 * Display a listing of CmtsGws
	 *
	 * @return Response
	 */
	public function index()
	{
		$CmtsGws = CmtsGw::all();	// = "select * from cmts_gws;"

		return View::make('cmtsgws.index', compact('CmtsGws'));
		// compact() makes passed variables available to the view - like ->with($variable) statement
	}

	/**
	 * Show the form for creating a new CmtsGw
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('cmtsgws.create');
	}


	/**
	 * Show the form for editing the specified CmtsGw.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$CmtsGw = CmtsGw::find($id);
		$CmtsGw = CmtsGw::with('ippools')->find($id);	// string inside "with"-statement has to match the public method name

		return View::make('cmtsgws.edit', compact('CmtsGw'));
	}


	/**
	 * Store a newly created CmtsGw in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), CmtsGw::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		CmtsGw::create($data);

		return Redirect::route('cmts.index');
	}


	/**
	 * Update the specified CmtsGw in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$CmtsGw = CmtsGw::findOrFail($id);

dd($CmtsGw);
		$validator = Validator::make($data = Input::all(), CmtsGw::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$CmtsGw->update($data);

		return Redirect::route('cmts.index');
	}

	/**
	 * Remove the specified CmtsGw from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			// TODO: put to base controller -> make it generic
			foreach (Input::all()['ids'] as $id => $val)
				CmtsGw::destroy($id);
		}
		else
			CmtsGw::destroy($id);

		return $this->index();
		//return Redirect::route('cmts.index'); 
	}

}
