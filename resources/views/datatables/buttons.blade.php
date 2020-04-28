buttons: [
    {
        extend: 'print',
        className: 'btn-sm btn-primary',
        titleAttr: "{!! trans('helper.PrintVisibleTable') !!}",
        exportOptions: {columns: ':visible.content'}
    },
    {
        extend: 'collection',
        text: "{{ trans('view.jQuery_ExportTo') }}",
        titleAttr: "{!! trans('helper.ExportVisibleTable') !!}",
        className: 'btn-sm btn-primary',
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
                exportOptions: {columns: ':visible.content'}
            },
            {
                extend: 'pdfHtml5',
                text: "<i class='fa fa-file-pdf-o'></i> .PDF",
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
        ]
    },
    {
        extend: 'colvis',
        className: 'btn-sm btn-primary',
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
],
