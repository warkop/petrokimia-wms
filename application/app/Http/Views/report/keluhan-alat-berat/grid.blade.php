@extends('layout.app')

@section('title', 'Rencana Harian')

@section('content')

<script>
    document.getElementById('report-keluhan-alat-berat-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Laporan Keluhan Alat Berat
                </h4>
                <p class="sub">
                    Berikut ini adalah form report keluhan alat berat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
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
                <h4 class="col-2 col-form-label text-kiri">Jenis Alat Berat</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="alatberat" name="param" multiple="multiple" style="width: 100%">
                        <option value="aa">Excavator</option>
                        <option value="AK">Truck</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Status Tindak Lanjut</h4>
                <div class="col-6">
                    <select class="form-control">
                        <option>Sudah</option>
                        <option>Belum</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        <a href="{{asset('assets/reports/keluhan-alat-berat/keluhan-alat-berat.xlsx')}}" class="btn btn-success"> <i class="fa fa-print"></i> Cetak Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#alatberat').select2({
        placeholder: "Pilih alat berat"
    });
    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        orientation: "top left"
    });
</script>


@stop