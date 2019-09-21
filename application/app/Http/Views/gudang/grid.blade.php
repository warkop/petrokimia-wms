@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')



<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Gudang
                </h4>
                <p class="sub">
                    Berikut ini adalah data gudang untuk menyimpan alat berat yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
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
                        <th>Nama Gudang</th>
                        <th>Minimal Pallet</th>
                        <th>Minimal Terplas</th>
                        <th>Jumlah Pupuk</th>
                        <th>Jumlah Alat Berat</th>
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
                                <label>Nama Gudang</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama gudang">
                            </div>
                            <div class="form-group">
                                <label>Jumlah Minimal Pallet</label>
                                <input type="text" class="form-control" placeholder="Masukkan minimal pallet">
                            </div>
                            <div class="form-group">
                                <label>Jumlah Minimal Terplas</label>
                                <input type="text" class="form-control" placeholder="Masukkan minimal terplas">
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

<!--begin::Modal jumlah pupuk -->
<div class="modal fade" id="kt_modal_pupuk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Jumlah Pupuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pupuk</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>Pupuk Urea</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">2</td>
                                        <td>Pupuk ZA</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">3</td>
                                        <td>Pupuk SP-36</td>
                                        <td>80</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">4</td>
                                        <td>Pupuk Rock Phospate</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">5</td>
                                        <td>Petro Nitrat</td>
                                        <td>10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal jumlah alat berat -->
<div class="modal fade" id="kt_modal_alat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Jumlah Alat Berat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori Alat Berat</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>Excavator</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">2</td>
                                        <td>Alat Pengangkut (Truk)</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">3</td>
                                        <td>Crane</td>
                                        <td>80</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">4</td>
                                        <td>Compactor</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">5</td>
                                        <td>Forklift</td>
                                        <td>10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->





<script src="{{asset('assets/extends/js/page/gudang.js')}}" type="text/javascript"></script>
@endsection
