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
{{--

Group blade: expandable group box, like panel, but without droppable

@vars:
	$header: 	Panel Header
	$content: 	the yield content section varibale
				NOTE: take care to not overwrite other vars, like content_1
	$expand:	if true, panel is expanded, default: is _not_ expanded

--}}

	<div class="panel panel-inverse">
		<div class="panel-heading">
			<h3 class="panel-title">
				<a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{$content}}" aria-expanded="false">
					<i class="fa fa-plus-circle pull-right"></i>
					{{$header}}
				</a>
			</h3>
		</div>
		<div id="{{$content}}" class="panel-collapse collapse {{(isset($expand) && $expand ? 'show' : '') }}" aria-expanded="true" style="">
			<div class="panel-body">
				@yield($content)
			</div>
		</div>
	</div>
