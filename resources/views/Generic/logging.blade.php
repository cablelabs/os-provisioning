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
<div class="card-block">
    <div id="logging" class="col-md-12 card tab-content" style="display:none;">
        <table id="datatable" class="table table-hover datatable table-bordered d-table">
            <thead>
                <tr>
                    <th class="nocolvis" style="min-width:20px;width:20px;"></th> {{-- Responsive Column --}}
                    <th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.guilog.created_at')}}</th>
                    <th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.guilog.username')}}</th>
                    <th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.guilog.method')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
