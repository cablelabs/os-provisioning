<?php

use Models\Modem;
use Models\Endpoint;
use Models\Configfile;
use Models\Quality;

class ModemsController extends \BaseController {

	/**
	 * Make Checkbox Default Input
	 * see: see http://forumsarchive.laravel.io/viewtopic.php?id=11627
	 */
	private function default_input ($data)
	{
		if(!isset($data['public']))$data['public']=0;
		if(!isset($data['network_access']))$data['network_access']=0;

		return $data;
	}

	/**
	 * return all Configfile Objects for CMs
	 */
	private function configfiles ()
	{
		return Configfile::where('device', '=', 'CM')->where('public', '=', 'yes')->get();
	}

	/**
	 * return a list [id => name] of all Configfile for CMs
	 */
	private function configfiles_list ()
	{
		$ret = array();
		foreach ($this->configfiles() as $cf)
		{
			$ret[$cf->id] = $cf->name;	
		}

		return $ret;
	}

	/**
	 * return a list of all qualitie profiles
	 */
	private function qualities_list ()
	{
		$ret = array();
		foreach (Quality::all() as $q)
		{
			$ret[$q->id] = $q->name;	
		} 
		return $ret;
	}

	/**
	 * Display a listing of modems
	 *
	 * @return Response
	 */
	public function index()
	{
		$modems = Modem::all();

		return View::make('modems.index', compact('modems'));
	}

	/**
	 * Show the form for creating a new modem
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('modems.create')->with('configfiles', $this->configfiles_list())->with('qualities', $this->qualities_list());
	}

	/**
	 * Store a newly created modem in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = $this->default_input(Input::all()), Modem::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Modem::create($data)->id;

		return Redirect::route('modem.edit', $id);
	}

	/**
	 * Display the specified modem.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$modem = Modem::findOrFail($id);

		return View::make('modems.show', compact('modem'));
	}

	/**
	 * Show the form for editing the specified modem.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$modem = Modem::find($id);
		
		return View::make('modems.edit', compact('modem'))->with('configfiles', $this->configfiles_list())->with('qualities', $this->qualities_list());
	}

	/**
	 * Update the specified modem in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$modem = Modem::findOrFail($id);

		$validator = Validator::make($data = $this->default_input(Input::all()), Modem::rules($id));
		
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$modem->update($data);

		return Redirect::route('modem.edit', $id);
	}

	/**
	 * Remove the specified modem from storage.
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			// TODO: put to base controller -> make it generic
			foreach (Input::all()['ids'] as $id => $val)
				Modem::destroy($id);
		}
		else
			Modem::destroy($id);

		return Redirect::back();
	}
	
}
