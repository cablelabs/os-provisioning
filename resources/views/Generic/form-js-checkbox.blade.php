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

This blade is used for jquery (java script) realtime based showing/hiding of fields depending on checkbox state.

NOTE: will be used from form-js blade and must be called inside javascript context

@param id the id of the triggering checkbox
@author Patrick Reichel

--}}

function par__toggle_class_visibility_depending_on_checkbox(id) {

    show_class = 'show_on_' + id;
    hide_class = 'hide_on_' + id;

    if ($('#' + id).prop('checked')) {
        $('.' + show_class).show();
        $('.' + hide_class).hide();
    }
    else {
        $('.' + show_class).hide();
        $('.' + hide_class).show();
    };
};
