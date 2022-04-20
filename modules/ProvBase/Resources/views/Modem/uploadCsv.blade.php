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
{{ Form::open([
    'method' => 'post',
    'route' => $context['route'],
    'enctype' => 'multipart/form-data',
])}}

    <input style="background-color:whitesmoke" name="modem_csv_upload" type="file" id="modem_csv_upload">
    <input type="hidden" name="method" value="{{ $context['method'] }}" />
    <input type="hidden" name="contract_id" value="{{ $context['contract'] }}" />
    <input type="hidden" name="redirect_url" value="{{ url()->current() }}" />
    <button type="submit" class="btn btn-primary" style="simple" name="upload" value="upload" title="boo">Upload CSV</button>

{{ Form::close() }}
