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
  // TODO: Make them dynamically!
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
  } ],
  // "sPaginationType": "four_button"
  lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "<?php echo trans('view.jQuery_All'); ?>" ] ],
  });

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
};
</script>
