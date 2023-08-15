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
        <div class="panel-heading flex flex-row justify-content-between dark:py-1 dark:px-2">
            <h4 class="panel-title flex">
                {!! $view_header !!}
            </h4>
            <div class="panel-heading-btn flex flex-row">
                <a href="javascript:;"
                    class="btn btn-xs btn-icon btn-circle btn-default flex"
                    data-click="panel-expand"
                    style="justify-content: flex-end;align-items: center">
                    <i class="fa fa-expand"></i>
                </a>
                <!--a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a-->
                <a href="javascript:;"
                    class="btn btn-xs btn-icon btn-circle btn-warning flex"
                    data-click="panel-collapse"
                    style="justify-content: flex-end;align-items: center">
                    <i class="fa fa-minus mt-1"></i>
                </a>
                <a href="javascript:;"
                    class="btn btn-xs btn-icon btn-circle btn-danger flex"
                    data-click="panel-remove"
                    style="justify-content: flex-end;align-items: center">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
