@extends('layout.app')

@section('title', 'Tambah Aktivitas')

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

                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
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
                    <div class="kel mb2">
                        <div class="row">
                            <div class="col-4 col-form-label">
                                <label class="kt-checkbox kt-checkbox--brand">
                                    <input type="checkbox"> Produk
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <select class="form-control kt-selectpicker">
                                    <option>Pilih jenis</option>
                                    <option>Mengurangi</option>
                                    <option>Menambah</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="kel mb2">
                        <div class="row">
                            <div class="col-4 col-form-label">
                                <label class="kt-checkbox kt-checkbox--brand">
                                    <input type="checkbox"> Pallet stok
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <select class="form-control kt-selectpicker">
                                    <option>Pilih jenis</option>
                                    <option>Mengurangi</option>
                                    <option>Menambah</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-form-label">
                                <label class="kt-checkbox kt-checkbox--brand">
                                    <input type="checkbox"> Pallet dipakai
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <select class="form-control kt-selectpicker">
                                    <option>Pilih jenis</option>
                                    <option>Mengurangi</option>
                                    <option>Menambah</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-form-label">
                                <label class="kt-checkbox kt-checkbox--brand">
                                    <input type="checkbox"> Pallet kosong
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <select class="form-control kt-selectpicker">
                                    <option>Pilih jenis</option>
                                    <option>Mengurangi</option>
                                    <option>Menambah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kel">
                <div class="row">
                    <div class="col-md-4 col-lg-4">
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
                                    <input type="checkbox"> Pengiriman
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="row form-group mb-0 mb2">
                            <div class="col-6 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Fifo
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group mb-0 mb2">
                            <div class="col-12 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Tidak pengaruh tgl produksi
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group mb-0 mb2">
                            <div class="col-12 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Pengiriman Gudang Internal
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="row form-group mb-0 mb2">
                            <div class="col-6 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Butuh alat berat
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group mb-0 mb2">
                            <div class="col-12 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Butuh TKBM
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group mb-0 mb2">
                            <div class="col-12 offset-col-2">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                    <input type="checkbox"> Butuh Tanda Tangan
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="text" class="form-control" id="start_date" readonly placeholder="Select date">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="text" class="form-control" id="end_date" readonly placeholder="Select date">
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto">
                        <a href="#" class="btn btn-success btn-elevate btn-elevate-air""><i class=" la la-save"></i>
                            Simpan Data</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->





<script src="{{asset('assets/extends/js/page/master-aktivitas.js')}}" type="text/javascript"></script>
<script>
    $('.kt-selectpicker').selectpicker();
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "top left"
});
</script>
@endsection