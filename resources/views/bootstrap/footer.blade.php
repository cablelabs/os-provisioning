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
if (typeof(Storage) !== "undefined") {
//save minified s_state
var ministate = localStorage.getItem("minified-state");
if (ministate == "true") {
  $('#page-container').addClass('page-sidebar-minified');
} else {
  $('#page-container').removeClass('page-sidebar-minified');
}
var sitem = localStorage.getItem("sidebar-item");
var chitem = localStorage.getItem("clicked-item");
$('#' + sitem).addClass("expand");
$('#' + sitem + ' .sub-menu ').css("display", "block");
$('#sidebar .sub-menu li').click(function(event) {
    localStorage.setItem("clicked-item", $(this).attr('id'));
    if ($('.page-sidebar-minified') == true) {
      $('#' + sitem).addClass("expand");
    }
});
$('#' + chitem).addClass("active");
}else {
  console.log("sorry, no Web Storage Support - Cant save State of Sidebar")
}
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();

// Type anywhere to search in global search for keyword
$(document).on('keypress', function (event) {
  if ($('*:focus').length == 0 && event.target.id != 'globalsearch'){
      var code = (event.keyCode ? event.keyCode : event.which);
      // if (code !=34 && code != 33) {
      if ((code < 32 || code > 47) && (code < 112 || code > 145)) {
      $("#togglesearch").click();
      $("#globalsearch").focus().select();
      }
  }
});

  // Select2 Init - intelligent HTML select
  $(window).resize(function() {
  $('.select2').css('width', "100%");
  });
  $("select").select2();

  /* This bit can be used on the entire app over all pages and will work for both tabs and pills.
   * Also, make sure the tabs or pills are not active by default,
   * otherwise you will see a flicker effect at page load.
   * Important: Make sure the parent ul has an id. Thanks Alain
   * http://stackoverflow.com/posts/16984739/revisions
   */
  $(function() {
    var json, tabsState;
    $('a[data-toggle="pill"], a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      var href, json, parentId, tabsState;

      tabsState = localStorage.getItem("tabs-state");
      json = JSON.parse(tabsState || "{}");
      parentId = $(e.target).parents("ul.nav.nav-pills, ul.nav.nav-tabs").attr("id");
      href = $(e.target).attr('href');
      json[parentId] = href;

      return localStorage.setItem("tabs-state", JSON.stringify(json));
    });

    tabsState = localStorage.getItem("tabs-state");
    json = JSON.parse(tabsState || "{}");

    $.each(json, function(containerId, href) {
      return $("#" + containerId + " a[href=" + href + "]").tab('show');
    });

    $("ul.nav.nav-pills, ul.nav.nav-tabs").each(function() {
      var $this = $(this);
      if (!json[$this.attr("id")]) {
        return $this.find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
      }
    });
  });

  //Tree
  $('#jstree-default').jstree({
      'plugins': [ "html_data", "checkbox", "wholerow", "types", "ui", "search", "state"],
      "core": {
          "dblclick_toggle": true,
          "themes": {
              "responsive": true,
          }
      },
      "checkbox": {
          "cascade": "",
          "three_state": false,
          "whole_node" : false,
          "tie_selection" : false,
          "real_checkboxes": true
      },
      "state" : { "filter" : function (k) { delete k.core.selected; return k; } },
      "types": {
          "cm":{
            "icon": "fa fa-hdd-o text-warning fa-lg"
          },
          "mta": {
            "icon": "fa fa-fax text-info fa-lg"
          },
          "default": {
              "icon": "fa fa-file-code-o text-success fa-lg"
          }
      }
  });


  $('#jstree-default').on('select_node.jstree', function(e,data) {
      var link = data.node.a_attr.href;
      if (link != "#" && link != "javascript:;" && link != "") {
          document.location.href = link;
          return false;
      }
  });


  // trigger on Checkbox change and give
  // invisible form the name of selected id
  // @author: Christian
  $('#jstree-default').on("check_node.jstree uncheck_node.jstree", function (e, data) {
      if (data.node.state.checked) {
        document.getElementById('myField'+ data.node.id).name = data.node.id;
      } else {
        document.getElementById('myField'+ data.node.id).name = '';
      }
  });

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
