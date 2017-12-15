fnDrawCallback: function(oSettings) {
    if ( ($('#datatable tr').length <= this.api().page.info().length) && (this.api().page.info().page == 0) ){
        $('.dataTables_paginate').hide();
        $('.dataTables_info').hide();
    }
    if ($('#datatable tr').length >= this.api().page.info().length) {
        $('.dataTables_paginate').show();
        $('.dataTables_info').show();
    }
},
