<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-3.2.0.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-migrate-1.4.1.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/bootstrap4/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2-v4/vendor/select2/select2/dist/js/select2.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/DataTables/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/media/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/FixedHeader/js/dataTables.fixedHeader.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<!--[if lt IE 9]>
  <script src="{{asset('components/assets-admin/crossbrowserjs/html5shiv.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/respond.min.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/excanvas.min.js')}}"></script>
<![endif]-->
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/jszip.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/pdfmake.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/vfs_fonts.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/Buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion.rangeSlider.js')}}"></script>


<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{asset('components/nmsprime.js')}}"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script language="javascript">
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();
  NMS.init();
  {{-- init modals --}}
  $("#alertModal").modal();
});
</script>
