<!-- Mainly scripts -->
<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('backend/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('backend/js/inspinia.js') }}"></script>
<script src="{{ asset('backend/js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ asset('backend/library/library.js') }}"></script>
<script src="{{ asset('backend/plugin/jquery-ui.js') }}"></script>
<!-- Flot -->
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.spline.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.pie.js') }}"></script>
<!-- Peity -->
<script src="{{ asset('backend/js/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('backend/js/demo/peity-demo.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('backend/js/inspinia.js') }}"></script>
<script src="{{ asset('backend/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('backend/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- GITTER -->
<script src="{{ asset('backend/js/plugins/gritter/jquery.gritter.min.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('backend/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Sparkline demo data  -->
<script src="{{ asset('backend/js/demo/sparkline-demo.js') }}"></script>

<!-- ChartJS-->
<script src="{{ asset('backend/js/plugins/chartJs/Chart.min.js') }}"></script>

<!-- Toastr -->
<script src="{{ asset('backend/js/plugins/toastr/toastr.min.js') }}"></script>
@if (isset($config['js']) && is_array($config['js']))
    @foreach ($config['js'] as $key => $val)
        <script src="{{ asset($val) }}"></script>
    @endforeach
@endif
