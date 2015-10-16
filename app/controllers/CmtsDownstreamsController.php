<?php

use Models\CmtsDownstream;
use Models\CmtsGw;

class CmtsDownstreamsController extends \BaseController {



	public function mibs($index)
	{

    	return array (
	    	array('frequency', '.1.3.6.1.2.1.10.127.1.1.1.1.2.'.$index),
	    	array('modulation', '.1.3.6.1.2.1.10.127.1.1.1.1.4.'.$index, array('3' => 'qam64', '4' => 'qam256')),
	    	array('power', '.1.3.6.1.2.1.10.127.1.1.1.1.6.'.$index),
	    	array('alias', '.1.3.6.1.2.1.31.1.1.1.18.'.$index),
	    	array('description', '.1.3.6.1.2.1.2.2.1.2.'.$index)
    	);

    }



	/**
	 * Display a listing of cmtsdownstream
	 *
	 * @return Response
	 */
	public function index()
	{
		$cmtsdownstreams = CmtsDownstream::all();

		return View::make('cmtsdownstream.index', compact('cmtsdownstreams'));
	}

	/**
	 * Show the form for creating a new cmtsdownstream
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('cmtsdownstream.create');
	}

	/**
	 * Store a newly created cmtsdownstream in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), CmtsDownstream::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		CmtsDownstream::create($data);

		return Redirect::route('cmtsdownstream.index');
	}

	/**
	 * Display the specified cmtsdownstream.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cmtsdownstream = CmtsDownstream::findOrFail($id);

		return View::make('cmtsdownstream.show', compact('cmtsdownstream'));
	}

	/**
	 * Show the form for editing the specified cmtsdownstream.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$cmtsdownstream = CmtsDownstream::find($id);

		$snmp = new SnmpController($cmtsdownstream, $this->mibs($cmtsdownstream->index),'10.42.253.254', 'public');

		//$snmp->dd();

		$snmp->snmp_get_all();

		return View::make('cmtsdownstream.edit', compact('cmtsdownstream'));
	}

	/**
	 * Update the specified cmtsdownstream in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$cmtsdownstream = CmtsDownstream::findOrFail($id);

		$validator = Validator::make($data = Input::all(), CmtsDownstream::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$cmtsdownstream->update($data);

		return Redirect::route('cmtsdownstream.index');
	}

	/**
	 * Remove the specified cmtsdownstream from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		CmtsDownstream::destroy($id);

		return Redirect::route('cmtsdownstream.index');
	}

}
