@extends('layout.app')

@section('title', 'Data Aktivitas')

@section('content')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Tambah Data Master Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah tambah data master aktivitas pada <span class="text-ungu kt-font-bolder">Aplikasi
                        WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal"
                        data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            {{-- <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>No</th>
                        <th>Nama Aktivitas</th>
                        <th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
            </table>					 --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nama Aktivitas</label>
                        <input type="text" class="form-control" placeholder="Masukkan nama aktivitas">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Pallet stok
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Pallet kosong
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Pallet dipakai
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Terplas stok
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Pallet kosong
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Terplas dipakai
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox"> Produk
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%">
                                    <select class="form-control kt-selectpicker">
                                        <option>Pilih jenis</option>
                                        <option>Mengurangi</option>
                                        <option>Menambah</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="kel">
                <div class="row form-group mb-0 mb2">
                    <div class="col-6 offset-col-2">
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                            <input type="checkbox"> Upload Foto
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="row form-group mb-0 mb2">
                    <div class="col-6 offset-col-2">
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                            <input type="checkbox"> Connect Sistro
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="row form-group mb-0 mb2">
                    <div class="col-6 offset-col-2">
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                            <input type="checkbox"> Fifo
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="row form-group mb-0 mb2">
                    <div class="col-6 offset-col-2">
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                            <input type="checkbox"> Tidak pengaruh tgl produksi
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row form-group mb-0 mt2">
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
        

        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto">
                        <a href="#" class="btn btn-success btn-elevate btn-elevate-air""><i class="la la-save"></i> Simpan</a>
                    </div>
                </div>
            </div>
        </div>



    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->


<!--begin::Modal-->
{{-- <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                                <label>Nama Aktivitas</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama aktivitas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Pallet stok
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Pallet kosong
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Pallet dipakai
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Terplas stok
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Pallet kosong
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Terplas dipakai
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox"> Produk
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="50%">
                                            <select class="form-control kt-selectpicker">
                                                <option>Pilih jenis</option>
                                                <option>Mengurangi</option>
                                                <option>Menambah</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
</div> --}}
<!--end::Modal-->





<script src="{{asset('assets/extends/js/page/master-aktivitas.js')}}" type="text/javascript"></script>
<script>
    $('.kt-selectpicker').selectpicker();
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
</script>
@endsection