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
	<h4 style="text-align: center;">{{ trans('messages.nativeApps') }}</h4>
	@foreach ($nativeApps as $nativeApp)
		@foreach ($nativeApp as $category => $modules)
			<div class="btn">
				<span>
					<b>{{ $category }}</b>
				</span>
				<div class="widget row" style="text-align: center;">
					@foreach ($modules as $attr)
						<div>
							<a href="{{ $attr['link'] }}">
								<img title="{{ trans('view.'.$attr['description']) }}" src="{{ asset('images/apps/'.$attr['icon']) }}" class="mx-2" style="height: 100px;">
							</a>
							<p class="text-black dark:text-dark-gray-light" style="margin-top: 5px;">{{ $attr['name'] }}</p>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	@endforeach
	<h4 style="text-align: center;">{{ trans('messages.externalApps') }}</h4>
	@foreach ($externalApps as $name => $externalApp)
		@if (\Route::currentRouteName() == 'Apps.'.$externalApp['state'] && file_exists(public_path('images/'.$externalApp['icon'])))
			<div class="btn">
				<div class="widget row" style="text-align: center; padding-bottom: 25px;">
					<div style="height: 100px;">
						<a href="{{ $externalApp['state'] == 'active' ? $externalApp['link'] : $externalApp['website'] }}" style="display: flex;">
							<img title="{{ trans('view.'.$externalApp['description']) }}" src="{{ asset('images/'.$externalApp['icon']) }}" class="mx-2" style="height: 100px;">
						</a>
						<p class="text-black dark:text-dark-gray-light" style="margin-top: 5px;">{{ $name }}</p>
					</div>
				</div>
			</div>
		@endif
	@endforeach
@stop
