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
        <form action="{{url('report/aktivitas-harian')}}" method="GET" target="_blank">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah form report aktivitas pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
        </div>
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Aktivitas</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="aktivitas" name="aktivitas[]" multiple="multiple" style="width: 100%">
                        @foreach ($aktivitas as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="gudang" name="gudang[]" multiple="multiple" style="width: 100%">
                        @foreach ($gudang as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Shift</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="shift" name="shift[]" multiple="multiple" style="width: 100%">
                        @foreach ($shift as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mt2" style="margin-bottom: 0;">
                <h4 class="col-2 col-form-label text-kiri">Tanggal Awal</h4>
                <div class="col-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="start_date" name="tgl_awal" readonly
                            placeholder="Pilih tanggal">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Tanggal Akhir</h4>
                <div class="col-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="end_date" name="tgl_akhir" readonly
                            placeholder="Pilih tanggal">
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        {{-- <a href="{{asset('assets/reports/aktivitas/aktivitas.xlsx')}}" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</a> --}}
                        <button type="submit" name="cetak" value="true" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</button>
                        <button type="submit" name="preview" value="true" class="btn btn-warning" download=""> <i class="fa fa-binoculars "></i> Preview Laporan</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<!-- end:: Content -->

{{-- <script src="{{asset('assets/extends/js/page/reportAktivitas.js')}}" type="text/javascript"></script> --}}
<script>
$('#gudang').select2({
    placeholder: "Semua gudang",
    allowClear: true
});
$('#shift').select2({
    placeholder: "Semua shift",
    allowClear: true
});
$('#aktivitas').select2({
    placeholder: "Semua aktivitas",
    allowClear: true
});

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