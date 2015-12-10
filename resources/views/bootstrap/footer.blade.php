
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
      <!-- ================== END BASE JS ================== -->

      <!-- ================== BEGIN PAGE LEVEL JS ================== -->
      <script src="{{asset('components/assets-admin/js/apps.min.js')}}"></script>
      <!-- ================== END PAGE LEVEL JS ================== -->


      <script>
        $(document).ready(function() {
          App.init();
          <!-- Dashboard.init(); -->
        });
      </script>

