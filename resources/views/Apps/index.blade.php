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
@extends ('Layout.split-nopanel')

@section ('content_left')
	@foreach ($apps as $app)
		<h4 style="text-align: center;"></h4>
		@foreach ($app as $category => $modules)
			<div class="btn">
				<span>
					<b>{{ $category }}</b>
				</span>
				<div class="widget row" style="text-align: center;">
					@foreach ($modules as $attr)
						<div>
							<a href="{{ $attr['link'] }}">
								<img title="{{ $attr['description'] }}" src="{{ asset('images/apps/'.$attr['icon']) }}" style="height: 100px; margin-right: 10px; margin-left: 10px;">
							</a>
							<p style="margin-top: 5px; color: black;">{{ $attr['name'] }}</p>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	@endforeach
@stop
