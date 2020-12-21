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
                    List Area
                </h4>
                <p class="sub">
                    Berikut ini adalah data area yang terdapat pada gudang <span class="text-ungu kt-font-bolder">{{$nama_gudang}}.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama area</th>
                        <th scope="col">Kapasitas (Ton)</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Range</th>
                        <th scope="col">Actions</th>
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
            <form id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <input type="hidden" name="action" id="action" value="add">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Area</label><br>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama area">
                            </div>
                            <div class="form-group">
                                <label>Kapasitas Ton</label>
                                <input type="text" class="form-control" onkeypress="return isNumberKey(this,event)"  name="kapasitas" id="kapasitas" placeholder="Masukkan kapasitas ton">
                                <span class="kt-font-warning">NB: </span><span class="kt-font-danger kt-font-bold">Pemisah koma menggunakan tanda titik. </span><span class="kt-font-primary kt-font-bold">Contoh: 3.14</span>
                            </div>
                            <div class="form-group">
                                <label>Jenis</label>
                                <div class="row">
                                    <div class="col-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="radio" value="1" name="tipe" id="indoor"> Indoor 
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="radio" value="2" name="tipe" id="outdoor"> Outdoor
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="range_form">
                                <label>Range</label><br>
                                <input type="text" class="form-control" name="range" id="range" placeholder="Masukkan nama range">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in" id="btn_save">Simpan data</button>
                </div>
                <input type="hidden" class="form-control" id="id_gudang" name="id_gudang" value="{{$id_gudang}}">
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->


<script>
    $('#kt_select2_1').select2({
        placeholder: "Select a state"
    });
    const id_gudang = "{{ $id_gudang }}";
function isNumberKey(txt, evt) {
    const charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46) {
        //Check if the text already contains the . character
        if (txt.value.indexOf('.') === -1) {
            return true;
        } else {
            return false;
        }
    } else {
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
}

$('input[type=radio][name=tipe]').change(function() {
    if (this.value == '1') {
        $('#range_form').hide();
    }
    else if (this.value == '2') {
        $('#range_form').show();
    }
});
</script>
<script src="{{asset('assets/extends/js/page/list-area.js')}}" type="text/javascript"></script>
@endsection