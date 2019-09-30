@extends('layout.app')

@section('title', 'Stok Adjustment')

@section('content')

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Stok Adjustment
                </h4>
                <p class="sub">
                    Berikut ini adalah data stok adjustment yang terdapat pada <span class="text-ungu kt-font-bolder">Gudang A.</span>
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
                        <th>Tanggal</th>
                        <th>Foto</th>
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
    <div class="modal-dialog modal-lg" role="document">
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
                                <label>Tanggal</label>
                                <input type="text" class="form-control" id="end_date" readonly placeholder="Select date">
                            </div>
                            <div class="form-group">
                                <label>Foto</label>
                                <div class="kt-dropzone dropzone" action="inc/api/dropzone/upload.php" id="m-dropzone-one">
                                    <div class="kt-dropzone__msg dz-message needsclick">
                                        <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
                                        <span class="kt-dropzone__msg-desc">This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <h5>List produk</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="">No</th>
                                    <th width="">Nama</th>
                                    <th width="25%">Jenis</th>
                                    <th width="25%">Jumlah</th>
                                    <th width=""><button class="btn btn-success btn-elevate btn-icon btn-sm"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <select class="form-control m-select2" id="kt_select2_produk" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                            <option value="">Pilih produk</option>
                                            <option value="AK">Urea</option>
                                            <option value="HI">Za</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control kt-selectpicker" style="width: 100%;">
                                            <option>Menambah</option>
                                            <option>Mengurangi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="jumlah" class="form-control" placeholder="Masukkan jumlah">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-elevate btn-icon btn-sm"><i class="la la-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <select class="form-control m-select2" id="kt_select2_produk2" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                            <option value="">Pilih produk</option>
                                            <option value="AK">Urea</option>
                                            <option value="HI">Za</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control kt-selectpicker" style="width: 100%;">
                                            <option>Menambah</option>
                                            <option>Mengurangi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="jumlah" class="form-control" placeholder="Masukkan jumlah">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-elevate btn-icon btn-sm"><i class="la la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <h5>List pallet</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="">No</th>
                                    <th width="">Nama</th>
                                    <th width="25%">Jenis</th>
                                    <th width="25%">Jumlah</th>
                                    <th width=""><button class="btn btn-success btn-elevate btn-icon btn-sm"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <select class="form-control m-select2" id="kt_select2_pallet3" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                            <option value="">Pilih produk</option>
                                            <option value="AK">Pallet A</option>
                                            <option value="HI">Pallet B</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control kt-selectpicker" style="width: 100%;">
                                            <option>Menambah</option>
                                            <option>Mengurangi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="jumlah" class="form-control" placeholder="Masukkan jumlah">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-elevate btn-icon btn-sm"><i class="la la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <h5>List terplas</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="">No</th>
                                    <th width="">Nama</th>
                                    <th width="25%">Jenis</th>
                                    <th width="25%">Jumlah</th>
                                    <th width=""><button class="btn btn-success btn-elevate btn-icon btn-sm"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <select class="form-control m-select2" id="kt_select2_terplas4" name="param" aria-placeholder="Pilih kategori" style="width: 100%;">
                                            <option value="">Pilih produk</option>
                                            <option value="AK">Terplas A</option>
                                            <option value="HI">Terplas B</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control kt-selectpicker" style="width: 100%;">
                                            <option>Menambah</option>
                                            <option>Mengurangi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="jumlah" class="form-control" placeholder="Masukkan jumlah">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-elevate btn-icon btn-sm"><i class="la la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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


<script src="{{asset('assets/extends/js/page/stok-adjustment.js')}}" type="text/javascript"></script>
<script>
$('.kt-selectpicker').selectpicker();
$('#kt_select2_produk, #kt_select2_produk2, #kt_select2_pallet3, #kt_select2_terplas4').select2({
    placeholder: "Select"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection
