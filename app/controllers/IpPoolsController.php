<?php

class IpPoolsController extends \BaseController {

	/**
	 * Display a listing of ippools
	 *
	 * @return Response
	 */
	public function index()
	{
		$ippools = IpPool::all();
		$hostnames = $this->cmts_hostnames();
		return View::make('ipPools.index', compact('ippools', 'hostnames'));
	}

	/**
	 * return all cmts hostnames for ip pools
	 * @author Nino Ryschawy
	 * @param
	 * @return $hostnames 		array of hostnames of all cmts`s ordered by their id
	 */
	private function cmts_hostnames ()
	{
		// $cmts_gws = CmtsGw::all(); //select('id', 'hostname');		-- doesnt work as is
		$cmts_gws = DB::table('cmts_gws')->select('id', 'hostname')->get();
		$hostnames = array();

		foreach ($cmts_gws as $host)
		{
			$hostnames[$host->id] = $host->hostname;
		}

		return $hostnames;
	}


	/**
	 * Show the form for creating a new ippool
	 *
	 * @return Response
	 */
	public function create()
	{
		$hostnames = $this->cmts_hostnames();
		return View::make('ipPools.create', compact('hostnames'));
	}


	/**
	 * Show the form for editing the specified ippool.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$ippool = IpPool::find($id);
		$hostnames = $this->cmts_hostnames();
		return View::make('ipPools.edit', compact('ippool', 'hostnames'));
	}


	/**
	 * Store a newly created ippool in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//dd(Input::all());
		$validator = Validator::make($data = Input::all(), IpPool::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		IpPool::create($data);

		return Redirect::route('ipPool.index');
	}

	/**
	 * Display the specified ippool.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$ippool = IpPool::findOrFail($id);

		return View::make('ipPools.show', compact('ippool'));
	}



	/**
	 * Update the specified ippool in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//dd(Input::all()); 		// dumps all the data that is received of the update method (from edit)

		$ippool = IpPool::findOrFail($id);

		$validator = Validator::make($data = Input::all(), IpPool::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$ippool->update($data);

		return Redirect::route('ipPool.index');
	}

	/**
	 * Remove the specified ippool from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			// TODO: put to base controller -> make it generic
			foreach (Input::all()['ids'] as $id => $val)
				IpPool::destroy($id);
		}
		else
			IpPool::destroy($id);

		return $this->index();
		//return Redirect::route('ipPool.index');
	}

}
