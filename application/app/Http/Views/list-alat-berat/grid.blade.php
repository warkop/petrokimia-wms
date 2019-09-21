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
                    List Alat Berat
                </h4>
                <p class="sub">
                    Berikut ini adalah data alat berat yang terdapat pada <span class="text-ungu kt-font-bolder">Gudang A.</span>
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
                        <th>Kategori Alat Berat</th>
                        <th>No. Polisi</th>
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
                                <label>Kategori Alat Berat</label><br>
                                <select class="form-control m-select2" id="kt_select2_1" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                    <option value="AK">Excavator</option>
                                    <option value="HI">Alat Pengangkut (Truk)</option>
                                    <option value="CA">Forklif</option>
                                    <option value="NV">Crane</option>
                                </select>
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
                    <button type="button" class="btn btn-orens">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->


<script src="{{asset('assets/extends/js/page/list-alat-berat.js')}}" type="text/javascript"></script>
<script>
$('#kt_select2_1').select2({
    placeholder: "Select a state"
});
</script>
@endsection
