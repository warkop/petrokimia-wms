@extends('layout.app')

@section('title', 'List Pallet')

@section('content')

<style>
.bg-navy-custom {
    background-color: #1f3364;
    color: #fff;
    border-bottom: 3px solid rgba(31, 51, 100, 0.87);
}
.kt-portlet.kt-portlet--border-bottom-navy {
    border-bottom: 3px solid rgba(31, 51, 100, 0.87);
}
</style>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!-- begin:: Widget -->
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-navy bg-navy-custom" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">20</span>
                            <span class="kt-widget26__desc">Pallet Dipakai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-navy" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">20</span>
                            <span class="kt-widget26__desc">Pallet Kosong</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-navy" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">1</span>
                            <span class="kt-widget26__desc">Pallet Rusak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Widget -->

    <div class="row">
        <div class="col-lg-12">
            <!--Begin::Dashboard 6-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-title">
                        <h4 class="kt-portlet__head-text title_sub pt-4">
                            {{-- <i class="la la-group"></i> &nbsp; --}}
                            List Pallet
                        </h4>
                        <p class="sub">
                            Berikut ini adalah list pallet yang terdapat pada <span class="text-ungu kt-font-bolder">Gudang A.</span>
                        </p>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group pt-4">
                            <a href="#" class="btn btn-orens" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Jenis</th>
                                <th width="30%;">Alasan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>					
                </div>
            </div>
            <!--End::Dashboard 6-->
        </div>
    </div>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" readonly placeholder="Select date" id="start_date" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="text" class="form-control" placeholder="Masukkan jumlah">
                            </div>
                            <div class="form-group">
                                <label>Jenis</label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" checked="checked" name="radio1"> Mengurangi 
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio"  name="radio1"> Menambah
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Alasan</label>
                                <textarea class="form-control" name="alasan" id="alasan" rows="3" placeholder="Masukkan alasan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-orens">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->



<script src="{{asset('assets/extends/js/page/list-pallet.js')}}" type="text/javascript"></script>
<script>
$('#start_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection
