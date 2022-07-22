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
initComplete: function () {
    this.api().columns().every(function () {
        var column = this;
        var input_filter_timeout;
        var input = document.createElement('input');

        input.classList.add('form-control');
        input.classList.add('input-sm');
        input.classList.add('select2');

        if ($(this.footer()).hasClass('searchable')){
            $(input).appendTo($(column.footer()).empty()).on('keyup', function (e) {
                var val = $(this).val();

                if (column.dataSrc() == 'mac' && val.length > 2) {
                    val = val.replace(/[\.\-]/g, ':');
                    var inputLength = val.length;
                    var mac = val.replace(/[\.\:\-]/g, '').match(/([a-f0-9]{1,2})/gi).join(':');

                    // if ':' has been added manually, then search this value
                    var wrongFormat = /(\:|^)[0-9a-f]{1}\:/gi;
                    if (inputLength <= mac.length || wrongFormat.test(val)) {
                        val = mac;
                    }

                    var caretPosition = this.selectionEnd;

                    $(this).val(val);
                    column.search(val);

                    // ignore arrow keys and ctrl+v
                    var code = e.keyCode || e.which
                    if (code == 8 || code == 46) {
                        this.selectionEnd = caretPosition;
                    } else if (code < 37 || code > 40 && code != 86) {
                        // change caret position if ':' was added via JS
                        this.selectionEnd = caretPosition + (val.length - inputLength);
                    }
                }

                clearTimeout(input_filter_timeout);

                input_filter_timeout = setTimeout(function() {
                    // https://datatables.net/reference/api/column().search() - params: input, regex, smart, caseInsen
                    // Setting smart search doesn't seem to have an influence when it's already set on server side (datatables.php)
                    column.search(val ? val : '', false, true).draw();
                }, 500);
            });
        }

        $('.select2').css('width', "100%");
    });

    var state = this.api().state.loaded();
    if (state) {
        this.api().columns().eq(0).each(function (colIdx) {
            var colSearch = state.columns[colIdx].search.search;
            if (colSearch.search) {
                $('input', this.column(colIdx).footer()).val(colSearch);
            }
        });
    }
},
