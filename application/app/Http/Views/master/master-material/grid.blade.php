@extends('layout.app')

@section('title', 'Master Material')

@section('content')

<script>
    document.getElementById('master-material-nav').classList.add('kt-menu__item--active');
</script>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Material
                </h4>
                <p class="sub">
                    Berikut ini adalah data master material yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Material SAP</th>
                        <th>Nama Material</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->


<!--begin::Modal-->
<div class="modal fade btn_close_modal" id="modal_form" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form  id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ID Material SAP</label>
                                <input type="text" class="form-control input-enter" id="id_material_sap" name="id_material_sap" placeholder="Masukkan ID material SAP">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Material</label>
                                <input type="text" class="form-control input-enter" id="nama" name="nama" placeholder="Masukkan nama material">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Kategori Material</label>
                                <select class="form-control input-enter m-select2" id="kategori" name="kategori"
                                    aria-placeholder="Pilih kategori" style="width: 100%;" onchange="pilihKategori(this)">
                                    <option value="">Pilih Material</option>
                                    <option value="1">Produk</option>
                                    <option value="2">Pallet</option>
                                    <option value="3">Lain-lain</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Berat</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-enter" id="berat" name="berat" placeholder="Masukan berat material"
                                        aria-describedby="berat">
                                    <div class="input-group-append"><span class="input-group-text"
                                            id="berat">Kg</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Koefisien Palet</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-enter" id="koefisien_pallet" name="koefisien_pallet" placeholder="Masukan koefisien palet"
                                        aria-describedby="tonase">
                                    <div class="input-group-append"><span class="input-group-text"
                                            id="tonase">Ton</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control input-enter" id="start_date" name="start_date" readonly
                                    placeholder="Select date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control input-enter" id="end_date" name="end_date" readonly
                                    placeholder="Select date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in"  id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->





<script src="{{asset('assets/extends/js/page/master-material.js')}}" type="text/javascript"></script>
<script>
    $('#kt_select2_1').select2({
    placeholder: "Select Material"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    orientation: "bottom left"
});
</script>
@endsection