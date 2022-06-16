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
@php
    $colvis = $colvis ?? true;
@endphp
buttons: [
    {
        extend: 'print',
        className: 'btn-sm bg-gray-200 text-gray-800 border-gray-300',
        titleAttr: "{!! trans('helper.PrintVisibleTable') !!}",
        exportOptions: {columns: ':visible.content'}
    },
    {
        extend: 'collection',
        text: "{{ trans('view.jQuery_ExportTo') }}",
        titleAttr: "{!! trans('helper.ExportVisibleTable') !!}",
        className: 'btn-sm bg-gray-200 text-gray-800 border-gray-300',
        autoClose: true,
        buttons: [
            {
                extend: 'csvHtml5',
                text: "<i class='fa fa-file-code-o'></i> .CSV",
                exportOptions: {columns: ':visible.content'},
                fieldSeparator: ';'
            },
            {
                extend: 'excelHtml5',
                text: "<i class='fa fa-file-excel-o'></i> .XLSX",
                action: function (e, dt, button, config) {
                    $.ajax({
                          url: '{{ asset('components/assets-admin/plugins/jszip/dist/jszip.min.js') }}',
                          dataType: "script",
                          cache: true,
                          success: () => {
                            console.log(this)
                            $.fn.dataTableExt.buttons.excelHtml5.action.call(this, e, dt, button, config)
                          }
                        })
                },
                exportOptions: {columns: ':visible.content'}
            },
            {
                extend: 'pdfHtml5',
                text: "<i class='fa fa-file-pdf-o'></i> .PDF",
                action: function ( e, dt, node, config ) {
                    delete window.pdfMake
                    $.ajax({
                      url: '{{ asset('components/assets-admin/plugins/pdfmake/build/pdfmake.min.js') }}',
                      dataType: "script",
                      cache: true,
                      success: () => {
                        $.ajax({
                            url: '{{ asset('components/assets-admin/plugins/pdfmake/build/vfs_fonts.js') }}',
                            dataType: "script",
                            cache: true,
                            success: () => {
                                $.fn.dataTableExt.buttons.pdfHtml5.action.call(this, e, dt, node, config )
                            }
                        })
                      }
                    })
                },
                exportOptions: {
                    columns: ':visible.content'
                    },
                customize: function(doc, config) {
                    var tableNode;
                    for (i = 0; i < doc.content.length; ++i) {
                        if(doc.content[i].table !== undefined){
                        tableNode = doc.content[i];
                        break;
                        }
                    }

                    var rowIndex = 0;
                    var tableColumnCount = tableNode.table.body[rowIndex].length;

                    if(tableColumnCount > 6){
                        doc.pageOrientation = 'landscape';
                    }
                },
            },
            {
                extend: 'print',
                text: "<i class='fa fa-print'></i> {{ trans('view.jQuery_Print') }}",
                exportOptions: { columns: ':visible.content' },
            },
            {
                extend: 'copy',
                text: "<i class='fa fa-clipboard'></i> {{ trans('view.jQuery_copyToClipboard') }}",
                exportOptions: { columns: ':visible.content' },
            }
        ]
    },
    @if($colvis)
    {
        extend: 'colvis',
        className: 'btn-sm bg-gray-200 text-gray-800 border-gray-300',
        titleAttr: "{!! trans('helper.ChangeVisibilityTable') !!}",
        columns: ':not(.nocolvis)',
        postfixButtons: [
            {
                extend:'colvisGroup',
                className: 'dt-button btn-warning',
                text:"{{ trans('view.jQuery_colvisReset') }}",
                show:':hidden'
            },
        ],
    },
    @endif
    {
        text: "{{ trans('dt_header.buttons.clearFilter') }}",
        className: 'btn-sm bg-gray-200 text-gray-800 border-gray-300',
        titleAttr: "{!! trans('helper.ClearFilter') !!}",
        action: function ( e, dt, node, config ) {
            dt.columns().eq(0).each(function (colIdx) {
                $('input', this.column(colIdx).footer()).val('')
            })

            dt.columns().search('')
            dt.table().search('')
            dt.table().draw()
        }
    },
],
