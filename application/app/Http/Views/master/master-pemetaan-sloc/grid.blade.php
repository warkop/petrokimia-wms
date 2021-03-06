@extends('layout.app')

@section('title', 'Master Pemetaan Sloc')

@section('content')

<script>
    document.getElementById('master-pemetaanSloc-nav').classList.add('kt-menu__item--active');
</script>


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Pemetaan Sloc
                </h4>
                <p class="sub">
                    Berikut ini adalah data master Pemetaan Sloc yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
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
                        <th scope="col">Nama Pemetaan</th>
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
<div class="modal fade btn_close_modal" id="modal_form" role="dialog" aria-labelledby="exampleModalLabel"
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
                                <label>Nama Pemetaan</label>
                                <input class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Pemetaan">
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom: .7em; padding: 1.25rem 1.25rem  0 1.25rem;">
                        <button type="button" class="btn btn-outline-success pull-right btn-sm" id="btnTambah"><i class="fa flaticon2-plus"></i> Tambah</button>
                    </div>
                    
                    <div class="kel" id="inputAdjst" value="0">
                            
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in" id="btn_save">Simpan
                        data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->
<script src="{{asset('assets/extends/js/page/master-pemetaan-sloc.js')}}" type="text/javascript"></script>
@endsection
