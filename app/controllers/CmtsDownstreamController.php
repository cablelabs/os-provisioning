<?php

use Models\CmtsDownstream;
use Models\CmtsGw;

class CmtsDownstreamController extends \SnmpController {



	public function mibs($index)
	{

    	return array (
	    	array('input',  'frequency', '.1.3.6.1.2.1.10.127.1.1.1.1.2.'.$index, 'i'),
	    	array('select', 'modulation', '.1.3.6.1.2.1.10.127.1.1.1.1.4.'.$index, 'i', array('3' => 'qam64', '4' => 'qam256')),
	    	array('input',  'power', '.1.3.6.1.2.1.10.127.1.1.1.1.6.'.$index, 'i'),
	    	array('input',  'alias', '.1.3.6.1.2.1.31.1.1.1.18.'.$index, 's'),
	    	array('input',  'description', '.1.3.6.1.2.1.2.2.1.2.'.$index, 's')
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

		return View::make('CmtsDownstream.index', compact('cmtsdownstreams'));
	}

	/**
	 * Show the form for creating a new cmtsdownstream
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('CmtsDownstream.create');
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

		return Redirect::route('CmtsDownstream.index');
	}

	/**
	 * Display the specified CmtsDownstream.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cmtsdownstream = CmtsDownstream::findOrFail($id);

		return View::make('CmtsDownstream.show', compact('cmtsdownstream'));
	}

	/**
	 * Show the form for editing the specified CmtsDownstream.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$cmtsdownstream = CmtsDownstream::find($id);

		$this->snmp_init($cmtsdownstream, $this->mibs($cmtsdownstream->index),'10.42.253.254', 'public');
		$this->snmp_get_all();

		$objects = $this->mibs($id);

		return View::make('CmtsDownstream.edit', compact('cmtsdownstream', 'objects'));
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

		$this->snmp_init($cmtsdownstream, $this->mibs($cmtsdownstream->index),'10.42.253.254', 'public');
		$this->snmp_set_all();

		return Redirect::route('CmtsDownstream.index');
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

		return Redirect::route('CmtsDownstream.index');
	}

}
