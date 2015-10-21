<?php

use Models\Mta;
use Models\Modem;
use Models\Configfile;

class MtaController extends \BaseController {

	/**
	 * return all modem objects
	 */
	private function modems()
	{
		return Modem::get();
	}


	/**
	 * return all Configfile Objects for MTAs
	 */
	private function configfiles()
	{
		return Configfile::where('device', '=', 'mta')->where('public', '=', 'yes')->get();
	}


	/**
	 * Display a listing of mtas
	 *
	 * @return Response
	 */
	public function index()
	{
		$mtas = Mta::all();

		return View::make('Mta.index', compact('mtas'));
	}


	/**
	 * Show the form for creating a new mta
	 *
	 * @return Response
	 */
	public function create()
	{
		$configfiles = $this->html_list($this->configfiles(), 'name');
		$modems = $this->html_list($this->modems(), 'hostname');
		$mta_types = Mta::getPossibleEnumValues('type', true);

		return View::make('Mta.create', compact('configfiles', 'modems', 'mta_types')); 
	}


	/**
	 * Show the form for editing the specified mta.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$mta = Mta::findOrFail($id);
		$configfiles = $this->html_list($this->configfiles(), 'name');
		$modems = $this->html_list($this->modems(), 'hostname');
		$mta_types = Mta::getPossibleEnumValues('type', true);

		return View::make('Mta.edit', compact('mta', 'configfiles', 'modems', 'mta_types')); 
	}

	/**
	 * Store a newly created mta in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Mta::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Mta::create($data)->id;

		return Redirect::route('Mta.edit', $id);
	}


	/**
	 * Update the specified mta in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$mta = Mta::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Mta::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$mta->update($data);

		return Redirect::route('Mta.index');
	}


	/**
	 * Remove the specified mta from storage.
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
				Mta::destroy($id);
		}
		else
			Mta::destroy($id);

		return Redirect::route('Mta.index');
	}

}
