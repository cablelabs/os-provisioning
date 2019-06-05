initComplete: function () {
    this.api().columns().every(function () {
        var column = this;
        var input = document.createElement('input');
        input.classList.add('form-control');
        input.classList.add('input-sm');
        input.classList.add('select2');
        if ($(this.footer()).hasClass('searchable')){
            $(input).appendTo($(column.footer()).empty())
            .on('keyup', function () {
                var val = $(this).val();

                column.search(val ? val : '', true, false).draw();
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
    $(this).DataTable().columns.adjust().responsive.recalc();
},
