<?php

use Models\Mta;
use Models\Modem;

class MtasController extends \BaseController {

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
		return View::make('mtas.create');
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

		Mta::create($data);

		return Redirect::route('mtas.index');
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

		return View::make('mtas.edit', compact('mta'));
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
