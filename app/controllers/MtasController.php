<?php

use Models\Mta;
use Models\Modem;
use Models\Configfile;

class MtasController extends \BaseController {

	// all currently available mta types
	public $MTA_TYPES = array(
		'sip' => "sip",
		'packetcable' => "packetcable",
	);


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
	private function modems_list($selected_modem)
	{
		$data = array();
		$data[0] = null;
		foreach ($this->modems() as $modem)
		{
			$data[$modem->id] = $modem->hostname;
		}

		$ret = array(
			'data' => $data,
			'selected' => $selected_modem,
		);

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
	private function configfiles_list($selected_configfile)
	{
		$data = array();
		$data[0] = null;
		foreach ($this->configfiles() as $cf)
		{
			$data[$cf->id] = $cf->name;
		}

		$ret = array(
			'data' => $data,
			'selected' => $selected_configfile,
		);

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
		return View::make('mtas.create')->with('configfiles', $this->configfiles_list())->with('modems', $this->modems_list());
	}


	/**
	 * Store a newly created mta in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Mta::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Mta::create($data)->id;

		return Redirect::route('mtas.edit', $id);
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
		$mta = Mta::find($id);

		$mta_types = array();
		$mta_types['data'] = $this->MTA_TYPES;
		$mta_types['selected'] = $mta['type'];


		return View::make('mtas.edit', compact('mta'))->with('configfiles', $this->configfiles_list($mta['configfile_id']))->with('modems', $this->modems_list($mta['modem_id']))->with('mta_types', $mta_types);
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

		$validator = Validator::make($data = Input::all(), Mta::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$mta->update($data);

		return Redirect::route('mtas.index');
	}


	/**
	 * Remove the specified mta from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Mta::destroy($id);

		return Redirect::route('mtas.index');
	}

}
