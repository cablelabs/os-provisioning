<?php

use Models\Cmts;
use Models\IpPool;

class CmtsController extends \BaseController {

	/**
	 * Display a listing of Cmts
	 *
	 * @return Response
	 */
	public function index()
	{
		$cmts = Cmts::all();

		return View::make('Cmts.index', compact('cmts'));
	}

	/**
	 * Show the form for creating a new Cmts
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Cmts.create');
	}


	/**
	 * Show the form for editing the specified Cmts.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$cmts = Cmts::find($id);

		return View::make('Cmts.edit', compact('cmts'));
	}


	/**
	 * Store a newly created Cmts in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Cmts::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Cmts::create($data);

		return Redirect::route('Cmts.index');
	}


	/**
	 * Update the specified Cmts in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$cmts = Cmts::findOrFail($id);

		//dd($Cmts);
		$validator = Validator::make($data = Input::all(), Cmts::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$cmts->update($data);

		return Redirect::route('Cmts.index');
	}

	/**
	 * Remove the specified Cmts from storage.
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
				Cmts::destroy($id);
		}
		else
			Cmts::destroy($id);

		return $this->index();
		//return Redirect::route('Cmts.index'); 
	}

}
