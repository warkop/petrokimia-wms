@extends('layout.app')

@section('title', 'Stok Adjustment')

@section('content')

<style>
.shine {
    background: #f6f7f8;
    background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
    background-repeat: no-repeat;
    background-size: 800px 104px; 
    display: inline-block;
    position: relative; 
    
    -webkit-animation-duration: 1s;
    -webkit-animation-fill-mode: forwards; 
    -webkit-animation-iteration-count: infinite;
    -webkit-animation-name: placeholderShimmer;
    -webkit-animation-timing-function: linear;
}

lines {
  height: 10px;
  margin-top: 10px;
  width: 200px; 
}

@-webkit-keyframes placeholderShimmer {
    0% {
        background-position: -468px 0;
    }
    
    100% {
        background-position: 468px 0; 
    }
}
</style>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Stock Adjustment
                </h4>
                <p class="sub">
                    Berikut ini adalah data stock adjustment yang terdapat pada <span class="text-ungu kt-font-bolder">{{$gudang->nama}}.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
					<button type="button" class="btn btn-wms btn-elevate btn-elevate-air" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</button>
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
<div class="modal fade btn_close_modal" id="modal_form" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="" id="form1" onsubmit="return false">
                <input type="hidden" class="form-control" id="id" name="id">
                <input type="hidden" name="action" id="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" name="tanggal" id="tanggal" readonly placeholder="Pilih tanggal" value="{{date('d-m-Y')}}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Foto</label>
                                {{-- <form action="/file-upload" class="kt-dropzone dropzone" id="m-dropzone-one">
                                    <div class="fallback">
                                        <input type="file" name="file" />
                                    </div>
                                </form> --}}
                                <div class="kt-dropzone dropzone" id="m-dropzone-one" >
                                    <div class="kt-dropzone__msg dz-message needsclick">
                                        <h3 class="kt-dropzone__msg-title">Seret berkas atau klik untuk mengunggah</h3>
                                        <span class="kt-dropzone__msg-desc">Hanya berkas dengan format <strong>jpg, png, jpeg, gif</strong> yang diizinkan untuk diunggah</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>List Foto</label>
                                <div id="list">
                                            
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <h5>List produk</h5>
                        <table class="table" id="table_produk">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th width="20%">Nama</th>
                                    <th width="20%">Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                    <th><button type="button" class="btn btn-success btn-elevate btn-icon btn-sm" onclick="tambahProduk()"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <h5>List pallet</h5>
                        <table class="table" id="table_pallet">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th width="20%">Nama</th>
                                    <th width="20%">Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                    <th width=""><button type="button" onclick="tambahPallet()" class="btn btn-success btn-elevate btn-icon btn-sm"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="mt-4">
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
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in" id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal Detail-->
<div class="modal fade btn_close_modal" id="modal_detail" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label for="">Tanggal</label>
                        <h5 id="tempat_tanggal"></h5>
                    </div>
                    <div class="col-md-6">
                        <label for="">Foto</label>
                        <h5 id="tempat_gambar"><a id="tempat_link_gambar" target="_blank" href=""><img id="tempat_muncul_gambar" src="" alt="" width="50%"></a></h5>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">List produk</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama produk</th>
                                    <th>Jenis aktivitas</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody id="tubuh_produk">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">List pallet</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama pallet</th>
                                    <th>Jenis aktivitas</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody id="tubuh_pallet">
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<script>
$('.kt-selectpicker').selectpicker();
$('#kt_select2_produk, #kt_select2_produk2, #kt_select2_pallet3, #kt_select2_terplas4').select2({
    placeholder: "Select"
});
$('#tanggal').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    clearBtn:true,
    orientation: "bottom left"
});

const id_gudang = "{{$id_gudang}}"
</script>
<script src="{{asset('assets/extends/js/page/stock-adjustment.js')}}" type="text/javascript"></script>
@endsection
