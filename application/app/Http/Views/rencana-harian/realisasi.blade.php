@extends('layout.app')

@section('title', 'Tambah Rencana Harian')

@section('content')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Realisasi Rencana Harian
                </h4>
                <p class="sub">
                    Berikut ini adalah form realisasi rencana harian pada <span class="text-ungu kt-font-bolder">Aplikasi
                        WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">

                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead class="text-center">
                            <th width="10%">No</th>
                            <th width="20%">Material</th>
                            <th width="20%">Jumlah</th>
                            <th width="10%">Realisasi</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Sapu Ijuk</option>
                                        <option value="HI">Sekop</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah sisa">
                                </td>
                                <td class="text-center">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> 
                                        <span></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Sapu Ijuk</option>
                                        <option value="HI">Sekop</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah sisa">
                                </td>
                                <td class="text-center">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> 
                                        <span></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Sapu Ijuk</option>
                                        <option value="HI">Sekop</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah sisa">
                                </td>
                                <td class="text-center">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> 
                                        <span></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto text-right">
                        <a href="#" class="btn btn-success btn-elevate btn-elevate-air""><i class="la la-save"></i> Simpan Data</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->





<script src="{{asset('assets/extends/js/page/master-aktivitas.js')}}" type="text/javascript"></script>
<script>
$('.kt-selectpicker').selectpicker();
$('#kt_select2_3').select2({
    placeholder: "Select admin gudang",
});
$('#kt_select2_1, #kt_select2_operator, #kt_select2_loket, #kt_select2_checker').select2({
    placeholder: "Select Alat Berat"
});
$('.kt_select2_housekeeping').select2({
    placeholder: "Select Housekepping"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "top left"
});
</script>
@endsection