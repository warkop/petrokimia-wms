@extends('layout.app')

@section('title', 'Data Alat Berat')

@section('content')

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Kategori: Forklift
                </h4>
                <p class="sub">
                    Berikut ini adalah data alat berat Forklift yang terdapat pada <span
                        class="text-ungu kt-font-bolder">Gudang
                        A.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <button id="btntambahAdjst" class="btn btn-success btn-elevate btn-elevate-air"><i
                            class="la la-plus"></i> Tambah Data</button>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            {{-- <div class="row mb3">
                <h4 style="padding: 0 10px;">Kategori: Forklift</h4>
            </div> --}}
            <table class="table table-striped- table-bordered table-hover table-checkable">
                <thead id="inputAdjst">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nomor Lambung</th>
                        <th class="text-center">Nomor Polisi</th>
                        <th class="text-center">Actions</th>
                    </tr>
                        <tr class="text-center">
                            <td>1</td>
                            <td>
                                <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                                    aria-placeholder="Pilih No. Lambung" style="width: 100%;">
                                    <option value="AK">O1</option>
                                    <option value="HI">O2</option>
                                    <option value="HI">O3</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" placeholder="" value="F 6646 GH" disabled></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body"
                                    data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i
                                        class="flaticon-delete"></i> </button>
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td>2</td>
                            <td>
                                <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                                    aria-placeholder="Pilih No. Lambung" style="width: 100%;">
                                    <option value="AK">O1</option>
                                    <option value="HI">O2</option>
                                    <option value="HI">O3</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" placeholder="" value="B 7777 FG" disabled></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body"
                                    data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i
                                        class="flaticon-delete"></i> </button>
                            </td>
                        </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->


<!--begin::Modal-->
<div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nomor Lambung</label>
                                <input type="text" class="form-control" placeholder="Masukkan nomor lambung">
                            </div>
                            <div class="form-group">
                                <label>Nomor Polisi</label>
                                <input type="text" class="form-control" placeholder="Masukkan nomor polisi">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->


{{-- <script src="{{asset('assets/extends/js/page/list-alat-berat.js')}}" type="text/javascript"></script> --}}
<script>
    $('#kt_select2_1').select2({
    placeholder: "Select a state"
});
</script>

<script>
    $('.kt-selectpicker').selectpicker();
    $('#kt_select2_3').select2({
        placeholder: "Select admin gudang",
    });
    
    $('#HK-1').select2({
        placeholder: "Select alat berat",
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
<script>
$("#btntambahAdjst").click(function () {
        $("#inputAdjst").append(`
        <tr class="text-center">
            <td>3</td>
            <td>
                <select class="form-control m-select2 kt_select2_housekeeping" name="param" aria-placeholder="Pilih No. Lambung" style="width: 100%;">
                    <option value="AK">O1</option>
                    <option value="HI">O2</option>
                    <option value="HI">O3</option>
                </select>
            </td>
            <td><input type="text" class="form-control" placeholder="" value="F 6646 GH" disabled></td>
            <td>
            <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i class="flaticon-delete"></i> </button>
            </td>
        </tr>
        `);
    });
</script>
@endsection