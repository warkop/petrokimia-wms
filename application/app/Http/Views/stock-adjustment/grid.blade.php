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
						<th scope="col">No</th>
                        <th scope="col">Tanggal Kejadian</th>
                        <th scope="col">Foto</th>
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
    <div class="modal-dialog" role="document" style="max-width: 1200px;">
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
                                <label>Tanggal Kejadian</label>
                                <input type="text" class="form-control" name="tanggal" id="tanggal" readonly placeholder="Pilih tanggal" value="{{date('d-m-Y')}}">
                            </div>
                            <div class="form-group">
                                <label>Shift</label>
                                <select class="form-control" name="shift_id" id="shift_id" readonly placeholder="Pilih shift">
                                    @foreach ($shift as $item)
                                        <option value="{{$item->id}}">{{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Foto</label>
                                <div class="kt-dropzone dropzone" id="m-dropzone-one" >
                                    <div class="kt-dropzone__msg dz-message needsclick">
                                        <h3 class="kt-dropzone__msg-title">Seret berkas atau klik untuk mengunggah</h3>
                                        <span class="kt-dropzone__msg-desc">Hanya berkas dengan format <strong>jpg, png, jpeg, gif</strong> yang diizinkan untuk diunggah</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-12 mt-5">
                            <div class="form-group">
                                <label id="paraf" class="form-control-label">Unggah Foto</label>
                                <div class="row col-12 v-middle-flex-center">
                                    <div class="col-6 img-edit" style="padding:0">
                                        <img id="img_modal" src="" alt="" style="width:100%;height:160px;object-fit: cover;border-radius:.5rem">
                                    </div>
                                    <div class="col-6 mt1">
                                        <div class="row">
                                            <input id="fileInput" name="paraf" type="file" style="display:none;" accept="image/x-png,image/jpg,image/jpeg, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" onchange="document.getElementById('img_modal').src = window.URL.createObjectURL(this.files[0])" />
                                            <button type="button" class="btn btn-clean btn-bold btn-upper ml-5" onclick="document.getElementById('fileInput').click();" id="triggerTambahFoto"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{-- <label>List Foto</label>
                                <div id="list">
                                            
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <h5>List produk</h5>
                        <table class="table" id="table_produk">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col" width="20%">Nama</th>
                                    <th scope="col" width="10%">Area</th>
                                    <th scope="col">Tanggal Produksi</th>
                                    <th scope="col" width="15%">Jenis</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Alasan</th>
                                    <th scope="col"><button type="button" class="btn btn-success btn-elevate btn-icon btn-sm" onclick="tambahProduk()"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="mt-4">
                        <h5>List pallet</h5>
                        <table class="table" id="table_pallet">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col" width="20%">Nama</th>
                                    <th scope="col" width="20%">Jenis</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Alasan</th>
                                    <th scope="col" width=""><button type="button" onclick="tambahPallet()" class="btn btn-success btn-elevate btn-icon btn-sm"><i class="la la-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" class="form-control" id="id_gudang" name="id_gudang" value="{{$id_gudang}}">
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
                        <label for="">Tanggal Kejadian</label>
                        <h5 id="tempat_tanggal"></h5>
                    </div>
                    <div class="col-md-6">
                        <label for="">Foto</label>
                        <h5 id="tempat_gambar"><a id="tempat_link_gambar" target="_blank" href=""><img id="tempat_muncul_gambar" src="" alt="" width="50%"></a></h5>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label for="">Shift</label>
                        <h5 id="tempat_shift"></h5>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">List produk</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama produk</th>
                                    <th scope="col">Area</th>
                                    <th scope="col">Tanggal Produksi</th>
                                    <th scope="col">Jenis aktivitas</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Alasan</th>
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
                                    <th scope="col">No</th>
                                    <th scope="col">Nama pallet</th>
                                    <th scope="col">Jenis aktivitas</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Alasan</th>
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
