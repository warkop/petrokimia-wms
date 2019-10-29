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
                    Kategori: {{$nama_kategori}}
                </h4>
                {{-- <p class="sub">
                    Berikut ini adalah data alat berat {{$nama_kategori}} yang terdapat pada <span class="text-ungu kt-font-bolder">Gudang
                        A.</span>
                </p> --}}
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            {{-- <div class="row mb3">
                <h4 style="padding: 0 10px;">Kategori: Forklift</h4>
            </div> --}}
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Lambung</th>
                        <th>Status</th>
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
<div class="modal fade btn_close_modal" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
                                <label>Nomor Lambung</label>
                                <input type="text" class="form-control input-enter" name="nomor_lambung" id="nomor_lambung" placeholder="Masukkan nomor lambung">
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

<script>
    $('#kt_select2_1').select2({
    placeholder: "Select a state"
});
const id_kategori = "{{ $id_kategori }}";
</script>

<script src="{{asset('assets/extends/js/page/list-alat-berat.js')}}" type="text/javascript"></script>
@endsection