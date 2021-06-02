<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
@extends ('Layout.split')


@section('content_top')
	{!! $headline !!}
@stop


@section('content_left')

	{!! Form::open(['route' => [$model_pure.'.attach_oids', $view_var->id], 'method' => 'post']) !!}

		{!! Form::label('oids', 'Choose OIDs') !!}
		{!! Form::select('oid_id[]', $oids, null, ['multiple' => 'multiple']) !!}
		<br><br><br><br><br><br>
		{!! Form::submit('Attach OIDs') !!}

	{!! Form::close() !!}

@stop



@section('content_right')

	@section('content_right_1')

	{!! Form::open(['route' => [$model_pure.'.attach_oids', $view_var->id], 'method' => 'post']) !!}

		{!! Form::label('mibfile', 'Choose MIB-File') !!}
		{!! Form::select('mibfile_id', $mibs) !!}
		{!! Form::submit('Attach OIDs') !!}

	{!! Form::close() !!}

	@stop


	@section('content_right_2')

	{!! Form::open(['route' => [$model_pure.'.attach_oids', $view_var->id], 'method' => 'post']) !!}

		{!! Form::label('oid_list', 'OID-List') !!}
		{!! Form::textarea('oid_list', null, ['placeholder' => '1.3.6.1.2.1.1.4
		1.3.6.1.2.1.1.6']) !!}

		<div class="col-md-12">
			<br>
			{!! trans('messages.oid_list') !!}
		</div>

		{!! Form::submit('Attach OIDs') !!}

	{!! Form::close() !!}

	@stop


	@include ('bootstrap.panel', array ('content' => "content_right_1", 'view_header' => 'Attach all OIDs of a MIB', 'md' => 3))
	@include ('bootstrap.panel', array ('content' => "content_right_2", 'view_header' => 'Attach OIDs from a List of OIDs', 'md' => 3))

@stop
