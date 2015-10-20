<?php

use Models\Quality;

class QosController extends \BaseController {

	/**
	 * Display a listing of qualities
	 *
	 * @return Response
	 */
	public function index()
	{
		$qualities = Quality::all();

		return View::make('Qos.index', compact('qualities'));
	}

	/**
	 * Show the form for creating a new quality
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Qos.create');
	}

	/**
	 * Store a newly created quality in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Quality::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Quality::create($data)->id;

		return Redirect::route('Qos.edit', $id)->with(DB::getQueryLog());
	}

	/**
	 * Display the specified quality.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$quality = Quality::findOrFail($id);

		return View::make('Qos.show', compact('quality'));
	}

	/**
	 * Show the form for editing the specified quality.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$quality = Quality::find($id);

		return View::make('Qos.edit', compact('quality'));
	}

	/**
	 * Update the specified quality in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$quality = Quality::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Quality::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$quality->update($data);

		return Redirect::route('Qos.edit', $id)->with(DB::getQueryLog());
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
				Quality::destroy($id);
		}
		else
			Quality::destroy($id);

		return $this->index();
	}

}
