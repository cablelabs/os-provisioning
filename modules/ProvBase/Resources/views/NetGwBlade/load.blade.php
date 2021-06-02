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

This blade is for loading the correct NetGw company blade, like cisco

--}}

@if (View::exists('provbase::NetGwBlade.'.strtolower($view_var->company)))
	<pre>@include ('provbase::NetGwBlade.'.strtolower($view_var->company))</pre>
@else
	<b>Everything works fine! There is just no assigned configuration proposal for {{$view_var->series}} NetGw from {{$view_var->company}} until now.</b><br><br>
	Be the first one who creates a default proposal config in this
	<a href="https://github.com/nmsprime/nmsprime/tree/master/modules/ProvBase/Resources/views/NetGwBlade" target="_blank">Github folder</a>
	<br><br>
	The file must be called {{strtolower($view_var->company)}}.blade.php.
	For more information checkout cisco.blade.php and the function prep_netgw_config_page() in
	<a href="https://github.com/nmsprime/nmsprime/blob/master/modules/ProvBase/Entities/NetGw.php" target="_blank">NetGw.php at Github</a>
@endif
