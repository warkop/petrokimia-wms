@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')

<script>
    // $('body').addClass("kt-aside--minimize");
    document.getElementById('report-aktivitas-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Laporan Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah form report aktivitas pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            {{-- <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="{{url('/add-rencana-harian')}}" class="btn btn-wms btn-elevate btn-elevate-air"><i
                class="la la-plus"></i> Tambah Data</a>
        </div>
    </div> --}}
        </div>
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row mt2" style="margin-bottom: 0;">
                <h4 class="col-2 col-form-label text-kiri">Start Date</h4>
                <div class="col-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="start_date" name="start_date" readonly
                            placeholder="Select date">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">End Date</h4>
                <div class="col-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="end_date" name="end_date" readonly
                            placeholder="Select date">
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        <a href="{{asset('assets/reports/aktivitas/aktivitas.xlsx')}}" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end:: Content -->

{{-- <script src="{{asset('assets/extends/js/page/reportAktivitas.js')}}" type="text/javascript"></script> --}}
<script>
$('#start_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    orientation: "bottom left"
});
$('#end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    orientation: "top left"
});
</script>
@endsection