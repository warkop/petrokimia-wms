<!-- begin::Global Config(global config for global JS sciprts) -->
		<script>
			var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"dark": "#282a3c",
						"light": "#ffffff",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995"
					},
					"base": {
						"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
						"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
					}
				}
			};
		</script>

		<!-- end::Global Config -->
		<!--begin:: Global Mandatory Vendors -->
		<script src="{{aset_tema()}}vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/js-cookie/src/js.cookie.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/moment/min/moment.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/wnumb/wNumb.js" type="text/javascript"></script>

		<!--end:: Global Mandatory Vendors -->

		<!--begin:: Global Optional Vendors -->
		<script src="{{aset_tema()}}vendors/general/jquery-form/dist/jquery.form.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/bootstrap-select/dist/js/bootstrap-select.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/bootstrap-switch/dist/js/bootstrap-switch.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/dropzone/dist/dropzone.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
		<script src="{{aset_extends()}}plugin/select2-4.0.11/dist/js/select2.full.min.js" type="text/javascript"></script>
		<!--end:: Global Optional Vendors -->

		<!--begin::Global Theme Bundle(used by all pages) -->
		<script src="{{aset_tema()}}demo/default/base/scripts.bundle.js" type="text/javascript"></script>

		<!--end::Global Theme Bundle -->

		<!--begin::Page Vendors(used by this page) -->
		<script src="{{aset_tema()}}vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}app/custom/general/components/extended/sweetalert2.js" type="text/javascript"></script>
		<script src="{{aset_tema()}}vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
		<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
		<!--end::Page Vendors -->

		<!--begin::Page Scripts(used by this page) -->
		{{-- <script src="{{ aset_extends('plugin/autoNumeric.js') }}" type="text/javascript"></script> --}}
		<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0"></script>
		<!--end::Page Scripts -->
		
		<!--begin::Global App Bundle(used by all pages) -->
		<script src="{{aset_tema()}}app/bundle/app.bundle.js" type="text/javascript"></script>
		<!--end::Global App Bundle -->