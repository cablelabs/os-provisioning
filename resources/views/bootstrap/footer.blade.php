<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-3.2.0.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-migrate-1.4.1.min.js')}}"></script>

<script type="text/javascript">
  if (typeof window.jQuery == 'undefined') {
      document.write('<script src="{{asset('components/assets-admin/plugins/jquery/jquery-3.2.0.min.js')}}">\x3C/script>');
  }
</script>

<script src="{{asset('components/assets-admin/plugins/jquery-ui/ui/minified/jquery-ui.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<!--[if lt IE 9]>
  <script src="{{asset('components/assets-admin/crossbrowserjs/html5shiv.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/respond.min.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/excanvas.min.js')}}"></script>
<![endif]-->
<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2-v4/vendor/select2/select2/dist/js/select2.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/DataTables/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/media/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<script src="{{asset('components/assets-admin/js/ui-modal-notification.demo.js')}}"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{asset('components/nmsprime.js')}}"></script>
<!-- Javascript Tree View (for index page) -->
<!-- <script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script> -->
<!-- <script src="{{asset('components/assets-admin/js/ui-tree.demo.min.js')}}"></script> -->
<!-- ================== END PAGE LEVEL JS ================== -->
<script language="javascript">
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();
  NMS.init();

  @if (isset($links))
    @foreach($links as $name => $link)
      $("#settings-{{Str::slug($name,'_')}}" ).load( "{{$link}}/1/edit #editform", function(){
        $('[data-toggle="popover"]').popover();
      });
    @endforeach
  @endif
  
  // Intelligent Data Tables
  @if (isset($model) && isset($view_var) && isset($view_var->index_datatables_ajax_enabled) && method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ))
    $('table.datatable').DataTable(
    {
        // Translate Datatables
        language: {
            "sEmptyTable":        "<?php echo trans('view.jQuery_sEmptyTable'); ?>",
            "sInfo":              "<?php echo trans('view.jQuery_sInfo'); ?>",
            "sInfoEmpty":         "<?php echo trans('view.jQuery_sInfoEmpty'); ?>",
            "sInfoFiltered":      "<?php echo trans('view.jQuery_sInfoFiltered'); ?>",
            "sInfoPostFix":       "<?php echo trans('view.jQuery_sInfoPostFix'); ?>",
            "sInfoThousands":     "<?php echo trans('view.jQuery_sInfoThousands'); ?>",
            "sLengthMenu":        "<?php echo trans('view.jQuery_sLengthMenu'); ?>",
            "sLoadingRecords":    "<?php echo trans('view.jQuery_sLoadingRecords'); ?>",
            "sProcessing":        "<?php echo trans('view.jQuery_sProcessing'); ?>",
            "sSearch":            "<?php echo trans('view.jQuery_sSearch'); ?>",
            "sZeroRecords":       "<?php echo trans('view.jQuery_sZeroRecords'); ?>",
            "oPaginate": {
                "sFirst":         "<?php echo trans('view.jQuery_PaginatesFirst'); ?>",
                "sPrevious":      "<?php echo trans('view.jQuery_PaginatesPrevious'); ?>",
                "sNext":          "<?php echo trans('view.jQuery_PaginatesNext'); ?>",
                "sLast":          "<?php echo trans('view.jQuery_PaginatesLast'); ?>"
                },
            "oAria": {
                "sSortAscending": "<?php echo trans('view.jQuery_sLast'); ?>",
                "sSortDescending":"<?php echo trans('view.jQuery_sLast'); ?>"
                }
        },
        //auto resize the Table to fit the viewing device
        responsive: {
            details: {
                type: 'column',
            }
        },
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: '{{ isset($route_name) && $route_name!= "Config.index"  ? route($route_name.'.data') : "" }}',
        columns:[
                    {data: 'responsive', orderable: false, searchable: false},
            @if (isset($delete_allowed) && $delete_allowed == true)
                    {data: 'checkbox', orderable: false, searchable: false},
            @endif
            @if (isset($view_var->view_index_label_ajax()['index_header']))
                @foreach ($view_var->view_index_label_ajax()['index_header'] as $field)
                    @if ( starts_with($field, $view_var->view_index_label_ajax()["table"].'.'))
                        {data: '{{ substr($field, strlen($view_var->view_index_label_ajax()["table"]) + 1) }}', name: '{{ $field }}'},
                    @else
                        {data: '{{ $field }}', name: '{{$field}}', 
                        searchable: {{ isset($view_var->view_index_label_ajax()["sortsearch"][$field]) ? "false" : "true" }}, 
                        orderable:  {{ isset($view_var->view_index_label_ajax()["sortsearch"][$field]) ? "false" : "true" }} 
                        },
                    @endif
                @endforeach
            @endif
        ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var input = document.createElement('input');
                input.classList.add('select2');
                if ($(this.footer()).hasClass('searchable')){
                    $(input).appendTo($(column.footer()).empty())
                    .on('keydown', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column.search(val ? val : '', true, false).draw();
                    });
                }
                $('.select2').css('width', "100%");
            });
            $(this).DataTable().columns.adjust().responsive.recalc();
        },
        @if (isset($view_var->view_index_label_ajax()['orderBy']))
            order:
            @foreach ($view_var->view_index_label_ajax()['orderBy'] as $columnindex => $direction)
                @if (isset($delete_allowed) && $delete_allowed == true)
                    [{{$columnindex + 2}}, '{{$direction}}'],
                @else
                    [{{$columnindex + 1}}, '{{$direction}}'], 
                @endif
            @endforeach
        @endif   
        aoColumnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   [0]
        },
        @if (isset($delete_allowed) && $delete_allowed == true)
        {
            className: 'index_check',
            orderable: false,
            targets:   [1]
        },      
        @endif
        {
            targets :  "_all",
            className : 'ClickableTd',
        } ],
        // "sPaginationType": "four_button"
        lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "<?php echo trans('view.jQuery_All'); ?>" ] ],
    });
  @elseif (method_exists( BaseController::get_model_obj() , 'view_index_label' ))
	$('table.datatable').DataTable(
	{
	// Translate Datatables
	language: {
		"sEmptyTable":        "<?php echo trans('view.jQuery_sEmptyTable'); ?>",
		"sInfo":              "<?php echo trans('view.jQuery_sInfo'); ?>",
		"sInfoEmpty":         "<?php echo trans('view.jQuery_sInfoEmpty'); ?>",
		"sInfoFiltered":      "<?php echo trans('view.jQuery_sInfoFiltered'); ?>",
		"sInfoPostFix":       "<?php echo trans('view.jQuery_sInfoPostFix'); ?>",
		"sInfoThousands":     "<?php echo trans('view.jQuery_sInfoThousands'); ?>",
		"sLengthMenu":        "<?php echo trans('view.jQuery_sLengthMenu'); ?>",
		"sLoadingRecords":    "<?php echo trans('view.jQuery_sLoadingRecords'); ?>",
		"sProcessing":        "<?php echo trans('view.jQuery_sProcessing'); ?>",
		"sSearch":            "<?php echo trans('view.jQuery_sSearch'); ?>",
		"sZeroRecords":       "<?php echo trans('view.jQuery_sZeroRecords'); ?>",
		"oPaginate": {
			"sFirst":         "<?php echo trans('view.jQuery_PaginatesFirst'); ?>",
			"sPrevious":      "<?php echo trans('view.jQuery_PaginatesPrevious'); ?>",
			"sNext":          "<?php echo trans('view.jQuery_PaginatesNext'); ?>",
			"sLast":          "<?php echo trans('view.jQuery_PaginatesLast'); ?>"
			},
		"oAria": {
			"sSortAscending": "<?php echo trans('view.jQuery_sLast'); ?>",
			"sSortDescending":"<?php echo trans('view.jQuery_sLast'); ?>"
			}
	},
	//auto resize the Table to fit the viewing device
	responsive: {
		details: {
			type: 'column'
		}
	},
	aoColumnDefs: [ {
		className: 'control',
		orderable: false,
		targets:   [0]
    },
    @if (isset($delete_allowed) && $delete_allowed == true)
    {
        className: 'index_check',
        orderable: false,
        targets:   [1]
    },      
    @endif ],
	// "sPaginationType": "four_button"
	lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "<?php echo trans('view.jQuery_All'); ?>" ] ],
	});
  @endif

  $('table.streamtable').DataTable(
  {
  // Translate Datatables
  language: {
      "sEmptyTable":        "<?php echo trans('view.jQuery_sEmptyTable'); ?>",
      "sInfo":              "<?php echo trans('view.jQuery_sInfo'); ?>",
      "sInfoEmpty":         "<?php echo trans('view.jQuery_sInfoEmpty'); ?>",
      "sInfoFiltered":      "<?php echo trans('view.jQuery_sInfoFiltered'); ?>",
      "sInfoPostFix":       "<?php echo trans('view.jQuery_sInfoPostFix'); ?>",
      "sInfoThousands":     "<?php echo trans('view.jQuery_sInfoThousands'); ?>",
      "sLengthMenu":        "<?php echo trans('view.jQuery_sLengthMenu'); ?>",
      "sLoadingRecords":    "<?php echo trans('view.jQuery_sLoadingRecords'); ?>",
      "sProcessing":        "<?php echo trans('view.jQuery_sProcessing'); ?>",
      "sSearch":            "<?php echo trans('view.jQuery_sSearch'); ?>",
      "sZeroRecords":       "<?php echo trans('view.jQuery_sZeroRecords'); ?>",
      "oPaginate": {
          "sFirst":         "<?php echo trans('view.jQuery_PaginatesFirst'); ?>",
          "sPrevious":      "<?php echo trans('view.jQuery_PaginatesPrevious'); ?>",
          "sNext":          "<?php echo trans('view.jQuery_PaginatesNext'); ?>",
          "sLast":          "<?php echo trans('view.jQuery_PaginatesLast'); ?>"
          },
      "oAria": {
          "sSortAscending": "<?php echo trans('view.jQuery_sLast'); ?>",
          "sSortDescending":"<?php echo trans('view.jQuery_sLast'); ?>"
          }
  },
  //auto resize the Table to fit the viewing device
  responsive: {
      details: {
          type: 'column'
      }
  },
  paging: false,
  info: false,
  searching: false,
  aoColumnDefs: [ {
      className: 'control',
      orderable: false,
      targets:   [0]
  } ]
  });

});

function resizeIframe(obj) {
  setTimeout(function() {obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px'}, 3000);
}
</script>
