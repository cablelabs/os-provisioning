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
{{-- Modal --}}
<div class="modal fade" id="alertModal" role="dialog">
	<div class="modal-dialog">
		{{-- Modal content--}}
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('messages.alert') }}</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-{{$color}} m-b-0">
					<h4><i class="fa fa-info-circle"></i>{{ $message }}</h4>
					<p></p>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">{{ trans('messages.close') }}</a>
			</div>
		</div>
	</div>
</div>

{{-- not working gritter kept here as comment --}}
{{--
<div id="gritter-notice-wrapper">
	<div id="gritter-item-1" class="gritter-item-wrapper my-sticky-class" style="" role="alert">
		<div class="gritter-top"></div>
		<div class="gritter-item">
			<a class="gritter-close" href="#" tabindex="1" style="display: none;">Close Notification</a>
			<img src="assets/img/user-2.jpg" class="gritter-image">
			<div class="gritter-with-image">
				<span class="gritter-title">Welcome back, Admin!</span>
				<p>{{ Session::get('alert') }}</p>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="gritter-bottom"></div>
	</div>
</div>
--}}
