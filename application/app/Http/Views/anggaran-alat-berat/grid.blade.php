@extends('layout.app')

@section('title', 'Anggaran Alat Berat')

@section('content')



<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Anggaran Alat Berat
                </h4>
                <p class="sub">
                    Berikut ini adalah data anggaran alat berat yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
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
						<th scope="col">No</th>
                        <th scope="col">Paket Alat Berat</th>
                        <th scope="col">Harga (Rp)</th>
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
                                <label>Nama Paket</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama paket">
                            </div>
                            <div class="form-group">
                                <label>Kategori Alat Berat</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="kt-checkbox-list">
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Dozer
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Excavator
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Alat Pengangkut (Truk)
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="kt-checkbox-list">
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Crane
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Forklift
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--warning">
                                                <input type="checkbox"> Wheel
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                    <input type="text" class="form-control" placeholder="Masukkan harga" aria-describedby="basic-addon1">
                                </div>
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





<script src="{{asset('assets/extends/js/page/anggaran-alat-berat.js')}}" type="text/javascript"></script>
<script>
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection
