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
	 * return a list [id => hostname] of all modems
	 */
	private function modems_list()
	{
		$ret = array();
		foreach ($this->modems() as $modem)
		{
			$ret[$modem->id] = $modem->hostname;
		}

		return $ret;
	}


	/**
	 * return all Configfile Objects for MTAs
	 */
	private function configfiles()
	{
		return Configfile::where('device', '=', 'mta')->where('public', '=', 'yes')->get();
	}


	/**
	 * return a list [id => name] of all Configfile for MTAt
	 */
	private function configfiles_list()
	{
		$ret = array();
		foreach ($this->configfiles() as $cf)
		{
			$ret[$cf->id] = $cf->name;
		}

		return $ret;
	}


	/**
	 * Display a listing of mtas
	 *
	 * @return Response
	 */
	public function index()
	{
		$mtas = Mta::all();

		return View::make('mtas.index', compact('mtas'));
	}


	/**
	 * Show the form for creating a new mta
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('mtas.create')->with('configfiles', $this->configfiles_list())->with('modems', $this->modems_list())->with('mta_types', Mta::getPossibleEnumValues('type', true));
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

		return Redirect::route('mta.edit', $id);
	}


	/**
	 * Display the specified mta.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$mta = Mta::findOrFail($id);

		return View::make('mtas.show', compact('mta'));
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

		return View::make('mtas.edit', compact('mta'))->with('configfiles', $this->configfiles_list())->with('modems', $this->modems_list())->with('mta_types', Mta::getPossibleEnumValues('type', true));
;
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

		return Redirect::route('mta.index');
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

		return Redirect::route('mta.index');
	}

}
