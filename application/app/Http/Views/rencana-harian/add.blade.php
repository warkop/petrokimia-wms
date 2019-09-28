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
                    Tambah Data Rencana Harian
                </h4>
                <p class="sub">
                    Berikut ini adalah tambah data rencana harian pada <span class="text-ungu kt-font-bolder">Aplikasi
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" class="form-control" placeholder="Masukkan nama gudang" disabled value="12/09/2019">
                    </div>
                    <div class="form-group">
                        <label>Shift Kerja</label>
                        <select class="form-control kt-selectpicker" id="exampleSelect1">
                            <option>Shift 1</option>
                            <option>Shift 2</option>
                            <option>Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin Gudang</label>
                        <select class="form-control m-select2" id="kt_select2_3" name="param" multiple="multiple">
                            <option value="AK" selected>Sasmianto</option>
                            <option value="HI">Rahayu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin Loket</label>
                        <select class="form-control m-select2" id="kt_select2_4" name="param" multiple="multiple">
                            <option>Suryani</option>
                            <option>Joko</option>
                            <option>Ninda</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Operator Alat Berat</label>
                        <select class="form-control m-select2" id="kt_select2_5" name="param" multiple="multiple">
                            <option>Surya</option>
                            <option>Pak Dwi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alat Berat</label>
                        <select class="form-control m-select2" id="kt_select2_6" name="param" multiple="multiple">
                            <option value="AK">Forklift</option>
                            <option value="HI">Truck</option>
                            <option value="CA">Dozer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Checker</label>
                        <select class="form-control m-select2" id="kt_select2_7" name="param" multiple="multiple">
                            <option value="AK">Rahmi</option>
                            <option value="HI">Ganjar</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <th width="10%">No</th>
                            <th width="30%">Nama Housekepper</th>
                            <th>Area Kerja</th>
                            <th width="10%"><button class="btn btn-success btn-sm btn-block"><i class="fa fa-plus"></i> Tambah</button></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple" >
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Maya</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_11" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple">
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Suryati</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_12" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple">
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Suryati</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_13" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
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
                    <div class="col-lg-12 ml-lg-auto">
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
$('#kt_select2_4').select2({
    placeholder: "Select admin loket",
});
$('#kt_select2_5').select2({
    placeholder: "Select operator alat berat",
});
$('#kt_select2_6').select2({
    placeholder: "Select alat berat",
});
$('#kt_select2_7').select2({
    placeholder: "Select checker",
});


$('#kt_select2_11').select2({
    placeholder: "Select area kerja",
});

$('#kt_select2_12').select2({
    placeholder: "Select area kerja",
});

$('#kt_select2_13').select2({
    placeholder: "Select area kerja",
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