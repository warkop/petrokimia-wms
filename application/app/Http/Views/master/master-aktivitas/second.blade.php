@extends('layout.app')

@section('title', 'Tambah Aktivitas')

@section('content')

<!-- begin:: Content -->
<form id="form1" class="kt-form" action="" method="post">
    {{-- @csrf
    @method('PUT') --}}
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
                            <input type="text" class="form-control" name="aktivitas" id="aktivitas" placeholder="Masukkan nama aktivitas">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="kel mb2">
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" id="selector_produk_stok"> Produk
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="produk_stok" id="produk_stok">
                                        <option value="">Pilih jenis</option>
                                        <option value="1">Mengurangi</option>
                                        <option value="2">Menambah</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="kel mb2">
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" id="selector_pallet_stok"> Pallet stok
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="pallet_stok" id="pallet_stok">
                                        <option value="">Pilih jenis</option>
                                        <option value="1">Mengurangi</option>
                                        <option value="2">Menambah</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" id="selector_pallet_dipakai"> Pallet dipakai
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="pallet_dipakai" id="pallet_dipakai">
                                        <option value="">Pilih jenis</option>
                                        <option value="1">Mengurangi</option>
                                        <option value="2">Menambah</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" id="selector_pallet_kosong"> Pallet kosong
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="pallet_kosong" id="pallet_kosong">
                                        <option value="">Pilih jenis</option>
                                        <option value="1">Mengurangi</option>
                                        <option value="2">Menambah</option>
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
                                        <input type="checkbox" name="upload_foto" id="upload_foto"> Upload Foto
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="connect_sistro" id="connect_sistro"> Connect Sistro
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pengiriman" id="pengiriman"> Pengiriman
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_approval" id="butuh_approval"> Butuh Approval
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="fifo" id="fifo"> Fifo
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="tdk_pngruh_tgl_prdksi" id="tdk_pngruh_tgl_prdksi"> Tidak pengaruh tgl produksi
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pengiriman_gudang_internal" id="pengiriman_gudang_internal"> Pengiriman Gudang Internal
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_alat_berat" id="butuh_alat_berat"> Butuh alat berat
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_tkbm" id="butuh_tkbm"> Butuh TKBM
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_tanda_tangan" id="butuh_tanda_tangan"> Butuh Tanda Tangan
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
                            <input type="text" class="form-control" id="start_date" name="start_date" readonly placeholder="Select date">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="text" class="form-control" id="end_date" name="end_date" readonly placeholder="Select date">
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-12 ml-lg-auto">
                            <button type="submit" class="btn btn-wms btn-elevate btn-elevate-air ladda-button" data-style="zoom-in"  id="btn_save"><i class=" la la-save"></i>
                                Simpan Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Dashboard 6-->
    </div>
    <!-- end:: Content -->
</form>

<script src="{{asset('assets/extends/js/page/master-aktivitas.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(()=>{
        $("#produk_stok").attr('disabled',true);
        $("#produk_stok").selectpicker('refresh');

        $("#pallet_stok").attr('disabled',true);
        $("#pallet_stok").selectpicker('refresh');

        $("#pallet_dipakai").attr('disabled',true);
        $("#pallet_dipakai").selectpicker('refresh');

        $("#pallet_kosong").attr('disabled',true);
        $("#pallet_kosong").selectpicker('refresh');
    });
    $('.kt-selectpicker').selectpicker();
    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format:'dd-mm-yyyy',
        orientation: "top left"
    });

    $("#selector_produk_stok").change(function() {
        if(this.checked) {
            $("#produk_stok").attr('disabled',false);
            $("#produk_stok").selectpicker('refresh');
        } else {
            $("#produk_stok").val("").change();
            $("#produk_stok").attr('disabled',true);
            $("#produk_stok").selectpicker('refresh');
        }
    });

    $("#selector_pallet_stok").change(function() {
        if(this.checked) {
            $("#pallet_stok").attr('disabled',false);
            $("#pallet_stok").selectpicker('refresh');
        } else {
            $("#pallet_stok").val("").change();
            $("#pallet_stok").attr('disabled',true);
            $("#pallet_stok").selectpicker('refresh');
        }
    });

    $("#selector_pallet_dipakai").change(function() {
        if(this.checked) {
            $("#pallet_dipakai").attr('disabled',false);
            $("#pallet_dipakai").selectpicker('refresh');
        } else {
            $("#pallet_dipakai").val("").change();
            $("#pallet_dipakai").attr('disabled',true);
            $("#pallet_dipakai").selectpicker('refresh');
        }
    });

    $("#selector_pallet_kosong").change(function() {
        if(this.checked) {
            $("#pallet_kosong").attr('disabled',false);
            $("#pallet_kosong").selectpicker('refresh');
        } else {
            $("#pallet_kosong").val("").change();
            $("#pallet_kosong").attr('disabled',true);
            $("#pallet_kosong").selectpicker('refresh');
        }
    });
</script>
@endsection