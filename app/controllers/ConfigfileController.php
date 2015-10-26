<?php

use Models\Configfile;

class ConfigfileController extends \BaseController {


	/**
	 * Store a newly created configfile in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Configfile::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Configfile::create($data)->id;

		return View::make('Configfile.edit', $id);
	}


	/**
	 * Show the form for editing the specified configfile.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$configfile = Configfile::find($id);

		$parents = array('0' => 'Null');
		foreach (Configfile::all() as $cf)
		{
			if ($cf->id != $id)
				$parents[$cf->id] = $cf->name;	
		}

		return View::make('Configfile.edit', compact('configfile', 'parents'));
	}

	/**
	 * Update the specified configfile in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$configfile = Configfile::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Configfile::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$configfile->update($data);

		return View::make('Configfile.edit', $id)->with(DB::getQueryLog());
	}

}
