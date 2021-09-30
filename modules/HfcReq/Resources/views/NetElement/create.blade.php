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

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@include ('Generic.create')


{{-- Reload IP Pool calculation when ip pool netelementtype_id field [CM, MTA ..] changes --}}
<script language="javascript">

$('#netelementtype_id').change(function() {
    let url = new URL(location)
    let urlParams = new URLSearchParams(location.search)
    let selectfield = document.getElementById("netelementtype_id")

    urlParams.set('netelementtype_id', selectfield.options[selectfield.selectedIndex].value)
    url.search = urlParams.toString()

    location.href = url
})

</script>
