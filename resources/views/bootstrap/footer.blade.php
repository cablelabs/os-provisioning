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
<!-- ================== BEGIN BASE JS ================== -->
@if (request()->is('customer*'))
<script src="{{ asset('js/ccc.js') }}"></script>
@endif

@if (request()->is('admin*'))
<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{ mix('js/app.js') }}"></script>
@include('bootstrap.module-js')
@endif

<script src="{{asset('components/assets-admin/plugins/bootstrap4/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>

<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion.rangeSlider.js')}}"></script>

<!-- ================== END PAGE LEVEL JS ================== -->

@if (request()->is('admin*'))
<script language="javascript">
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();
  NMS.init();
  {{-- init modals --}}
  $("#alertModal").modal();
});
</script>
@endif
