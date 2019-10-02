@extends('layout.app')

@section('title', 'Master Jenis Foto')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Jenis Foto
                </h4>
                <p class="sub">
                    Berikut ini adalah data master jenis foto yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal"
                        data-target="#modal_form"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis</th>
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
<div class="modal fade btn_close_modal" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form  id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="jenis_foto_id" name="jenis_foto_id">
                    <input type="hidden" name="action" id="action" value="add">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Jenis</label>
                                <input type="text" class="form-control input-enter" name="nama_jenis_foto" id="nama_jenis_foto" placeholder="Masukkan nama jenis foto">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control input-enter" id="from_date" name="from_date" readonly placeholder="Select date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control input-enter" name="end_date" id="end_date" readonly placeholder="Select date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success ladda-button" data-style="zoom-in"  id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal-->
<div class="modal fade" id="kt_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Foto Truck</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="kt-scroll" data-scroll="true" data-height="400">
                        <div class="row mb2">
                            <div class="col-4">
                                <label class="boldd">Foto Atas</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product1.jpg')}}" alt="" srcset="">
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kiri</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product2.jpg')}}" alt="" srcset="">
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kanan</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product3.jpg')}}" alt="" srcset="">
                            </div>
                        </div>
                        <div class="row mb2">
                            <div class="col-4">
                                <label class="boldd">Foto Bawah</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product4.jpg')}}" alt="" srcset="">
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kosong</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product5.jpg')}}" alt="" srcset="">
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Muat</label>
                                <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product6.jpg')}}" alt="" srcset="">
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




<script src="{{asset('assets/extends/js/page/master-jenis-foto.js')}}" type="text/javascript"></script>
<script>
$('#from_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    orientation: "bottom left"
});
</script>
@stop