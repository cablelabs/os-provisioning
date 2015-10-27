<?php

use Models\Phonenumber;
use Models\Mta;

class PhonenumbersController extends \BaseController {

	/**
	 * return all mta objects
	 */
	private function mtas()
	{
		$dummies = Mta::withTrashed()->where('is_dummy', True)->get();
		$mtas = Mta::get();
		return array('dummies' => $dummies, 'mtas' => $mtas);
	}


	/**
	 * return a list [id => hostname] of all mtas
	 */
	private function mtas_list()
	{
		$ret = array();
		foreach ($this->mtas()['mtas'] as $mta)
		{
			$ret[$mta->id] = $mta->hostname;
		}

		return $ret;
	}


	/**
	 * return a list [id => hostname] of all mtas
	 */
	private function mtas_list_with_dummies()
	{
		$ret = array();
		foreach ($this->mtas() as $mta_tmp)
		{
			foreach ($mta_tmp as $mta)
			{
				$ret[$mta->id] = $mta->hostname;
			}
		}

		return $ret;
	}

	/**
	 * Display a listing of phonenumbers
	 *
	 * @return Response
	 */
	public function index()
	{
		$phonenumbers = Phonenumber::all();

		return View::make('phonenumbers.index', compact('phonenumbers'));
	}

	/**
	 * Show the form for creating a new phonenumber
	 *
	 * @return Response
	 */
	public function create()
	{
		// set mta_id if given (if phonenumber creation is started from mta edit view)
		$mta_id = Input::get('mta_id', 0);
		// don't use is_int as form data is always a string!
		if (!is_numeric($mta_id)) {
			$mta_id = 0;
		}

		return View::make('phonenumbers.create')->with('mtas', $this->mtas_list_with_dummies())->with('country_codes', Phonenumber::getPossibleEnumValues('country_code'))->with('mta_id', $mta_id);
	}

	/**
	 * Store a newly created phonenumber in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Phonenumber::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Phonenumber::create($data)->id;

		return Redirect::route('phonenumber.edit', $id);
	}

	/**
	 * Display the specified phonenumber.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$phonenumber = Phonenumber::findOrFail($id);

		return View::make('phonenumbers.show', compact('phonenumber'));
	}

	/**
	 * Show the form for editing the specified phonenumber.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$phonenumber = Phonenumber::findOrFail($id);

		return View::make('phonenumbers.edit', compact('phonenumber'))->with('mtas', $this->mtas_list_with_dummies())->with('country_codes', Phonenumber::getPossibleEnumValues('country_code'));
	}

	/**
	 * Update the specified phonenumber in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$phonenumber = Phonenumber::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Phonenumber::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$phonenumber->update($data);

		return Redirect::route('phonenumber.index');
	}

	/**
	 * Remove the specified phonenumber from storage.
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
				Phonenumber::destroy($id);
		}
		else
		{
			Phonenumber::destroy($id);
		}

		return Redirect::route('phonenumber.index');
	}

}
