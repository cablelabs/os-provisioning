
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
      <script src="{{asset('components/assets-admin/plugins/jquery-cookie/jquery.cookie.js')}}"></script>
      <!-- ================== END BASE JS ================== -->

      <!-- ================== BEGIN PAGE LEVEL JS ================== -->
      <script src="{{asset('components/assets-admin/plugins/gritter/js/jquery.gritter.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.time.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.resize.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.pie.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/sparkline/jquery.sparkline.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/jquery-jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
      <script src="{{asset('components/assets-admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
      <script src="{{asset('components/assets-admin/js/dashboard.min.js')}}"></script>
      <script src="{{asset('components/assets-admin/js/apps.min.js')}}"></script>
      <!-- ================== END PAGE LEVEL JS ================== -->


      <script>
        $(document).ready(function() {
          App.init();
          Dashboard.init();
        });
      </script>

