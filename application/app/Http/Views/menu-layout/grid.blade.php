@extends('layout.app')

@section('title', 'Layout')

@section('content')



<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Layout
                </h4>
                <p class="sub">
                    Berikut ini adalah layout yang terdapat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
					<a href="#" class="btn btn-success btn-elevate btn-elevate-air">
                        {{-- <i class="la la-plus"></i>  --}}
                        Edit Layout
                    </a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
            <div id="kt_gmap_1" style="height:500px;"></div>
		</div>
	</div>
	<!--End::Dashboard 6-->
</div>
<!-- end:: Content -->


@endsection
