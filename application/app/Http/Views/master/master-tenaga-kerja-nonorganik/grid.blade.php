@extends('layout.app')

@section('title', 'Master Tenaga Kerja Non Organik')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Tenaga Kerja Non Organik
                </h4>
                <p class="sub">
                    Berikut ini adalah data master tenaga kerja non-organik yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
					<a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>No</th>
                        <th>Nama</th>
                        <th>No. Hp</th>
                        <th>Pekerjaan</th>
                        <th>Nama Karu</th>
                        <th>Start Date</th>
                        <th>End Date</th>
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
                                <label>Nama Tenaga Kerja</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama tenaga kerja">
                            </div>
                            <div class="form-group">
                                <label>Nomor Hp</label>
                                <input type="text" class="form-control" placeholder="Ex. 0895340952989">
                            </div>
                            <div class="form-group">
                                <label>Nama Tenaga Kerja</label>
                                <select class="form-control m-select2" id="kt_select2_1" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                    <option value="">Pilih pekerjaan</option>
                                    <option value="AK">Admin</option>
                                    <option value="HI">Checker</option>
                                    <option value="CA">Loket</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Karu</label>
                                <select class="form-control m-select2" id="kt_select2_2" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                    <option value="">Pilih karu</option>
                                    <option value="AK">Irwan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control" id="start_date" readonly placeholder="Select date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control" id="end_date" readonly placeholder="Select date">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mb-0">
                        <label class="col-2 col-form-label">Status</label>
                        <div class="col-2">
                            <span class="kt-switch kt-switch--primary kt-switch--icon">
                                <label>
                                    <input type="checkbox" checked="checked" name="" />
                                    <span></span>
                                </label>
                            </span>
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





<script src="{{asset('assets/extends/js/page/master-tenaga-kerja.js')}}" type="text/javascript"></script>
<script>
$('#kt_select2_1').select2({
    placeholder: "Pilih pekerjaan"
});
$('#kt_select2_2').select2({
    placeholder: "Pilih karu"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection
