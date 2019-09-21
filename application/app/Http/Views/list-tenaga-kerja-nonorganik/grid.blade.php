@extends('layout.app')

@section('title', 'List Tenaga Kerja Non-organik')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="row">
        <div class="col-lg-12">
            <!--Begin::Dashboard 6-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-title">
                        <h4 class="kt-portlet__head-text title_sub pt-4">
                            {{-- <i class="la la-group"></i> &nbsp; --}}
                            List Tenaga Kerja Non Organik
                        </h4>
                        <p class="sub">
                            Berikut ini adalah list tenaga kerja non-organik yang bertugas di <span class="text-ungu kt-font-bolder">Gudang A.</span>
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
                                <th>Nama</th>
                                <th>Pekerjaan</th>
                                <th>Shift Kerja</th>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama</label>
                                <select class="form-control m-select2" id="kt_select2_1" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                    <option value="">Pilih Nama Tenaga Kerja</option>
                                    <option value="AK">Suryadi - Checker</option>
                                    <option value="HI">Yanto - Loket</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sift Kerja</label>
                                <select class="form-control kt-selectpicker">
                                    <option value="">Pilih shift kerja</option>
                                    <option value="HI">Sift 1 - 00.00-08.00 WIB</option>
                                    <option value="AK">Sift 2 - 08.00-16.00 WIB</option>
                                    <option value="HI">Sift 3 - 16.00-00.00 WIB</option>
                                </select>
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



<script src="{{asset('assets/extends/js/page/list-tenaga-kerja.js')}}" type="text/javascript"></script>
<script>
$('#kt_select2_1').select2({
    placeholder: "Pilih tenaga kerja"
});
$('.kt-selectpicker').selectpicker();
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection
