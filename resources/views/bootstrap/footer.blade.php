<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-1.9.1.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-migrate-1.1.0.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery-ui/ui/minified/jquery-ui.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<!--[if lt IE 9]>
  <script src="{{asset('components/assets-admin/crossbrowserjs/html5shiv.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/respond.min.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/excanvas.min.js')}}"></script>
<![endif]-->
<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2-v4/vendor/select2/select2/dist/js/select2.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/js/jquery.dataTables.min.js')}}"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.categories.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion-rangeSlider/ion.rangeSlider.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/switchery/switchery.js')}}"></script>
<script src="{{asset('components/assets-admin/js/form-slider-switcher.demo.js')}}"></script>

<script src="{{asset('components/assets-admin/js/apps.min.js')}}"></script>
<!-- Javascript Tree View (for index page) -->
<!-- <script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script> -->
<!-- <script src="{{asset('components/assets-admin/js/ui-tree.demo.min.js')}}"></script> -->
<!-- ================== END PAGE LEVEL JS ================== -->


<script type="text/javascript">

  /*
   * global document ready function
   */
  $(document).ready(function() {
    App.init();
    // Dashboard.init();

    // Select2 Init - intelligent HTML select
    $("select").select2();

    // Intelligent Data Tables
    $('.itable').dataTable( 
    {
      "language": {
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
      // "sPaginationType": "four_button"
      "lengthMenu":  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "<?php echo trans('view.jQuery_All'); ?>" ] ],
    });

//      $.plot($("#interactive-chart"),
//          [ [[0, 0], [1, 1]] ], {
//              yaxis: {max: 1}
//          });
  });


  /*
   * Table on-hover click
   * NOTE: This automatically adds on-hover click to all table 'td' elements which are in class 'ClickableTd'.
   *       Please note that the table needs to be in class 'table-hover' for visual marking.
   *
   * HOWTO:
   *  - If clicked on td element which is assigned in class ClickableTd the function bellow is called.
   *  - fetch parent element of td element, which should/(must?) be a row.
   *  - search in tr HTML code for an HTML "a" element and fetch the href attribute
   * INFO: - working directly with row element also adds a click object to checkbox entry, which disabled checkbox functionality
   */
  $('.ClickableTd').click(function () {
    window.location = $(this.parentNode).find('a').attr("href");
  });

</script>
