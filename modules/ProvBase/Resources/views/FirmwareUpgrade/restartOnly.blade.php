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
<script type="text/javascript">
    $(document).ready(function()
    {
        // If this firmware upgrade only requires a restart, hide the To Configfile should be left empty checkbox
        // and optionally allow the user to enter a list of regexp to match firmware versions
        $('#restart_only').change(function() {
            toggleToConfigfile();
            toggleFirmwareMatchString();
        });

        function toggleFirmwareMatchString() {
            if($('#restart_only').is(":checked")) {
                $('#firmware_match_string').closest('.form-group.row').parent().show();
            } else {
                $('#firmware_match_string').closest('.form-group.row').parent().hide();
            }
        }

        function toggleToConfigfile() {
            if($('#restart_only').is(":checked")) {
                // Clear any selected options and disable the select
                $('#to_configfile_id').val(null).trigger('change');
                $('#to_configfile_id').prop('disabled', true).trigger('change');
                $('#to_configfile_id').closest('.form-group.row').parent().hide();
            } else {
                $('#to_configfile_id').prop('disabled', false).trigger('change');
                $('#to_configfile_id').closest('.form-group.row').parent().show();
            }
        }

        toggleToConfigfile();
        toggleFirmwareMatchString();
    });
</script>
