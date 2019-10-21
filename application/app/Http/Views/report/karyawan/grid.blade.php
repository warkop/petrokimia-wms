@extends('layout.app')

@section('title', 'Rencana Harian')

@section('content')

<script>
    document.getElementById('report-absen-karyawan-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Laporan Absen Karyawan
                </h4>
                <p class="sub">
                    Berikut ini adalah form report absen karyawan pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
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
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="gudang" name="param" multiple="multiple" style="width: 100%">
                        <option value="aa">Gudang A</option>
                        <option value="AK">Gudang B</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Karyawan</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="Karyawan" name="param" multiple="multiple">
                        <option value="aa">Iriana Mangunsong</option>
                        <option value="AK">Gabriella Andriani</option>
                        <option value="cs">Luhung Wibowo</option>
                        <option value="cs">Gading Prakarsa</option>v
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Periode</h4>
                <div class="col-6">
                    <div class="kel-min">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" class="form-control" id="start_date" name="start_date" readonly
                                        placeholder="Select date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" class="form-control" id="end_date" name="end_date" readonly
                                        placeholder="Select date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        <a href="{{asset('assets/reports/absen-karyawan/absen-karyawan.xlsx')}}" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#gudang').select2({
        placeholder: "Pilih gudang"
    });
    $('#Karyawan').select2({
        placeholder: "Pilih Karyawan"
    });

    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        orientation: "top left"
    });
</script>


@stop