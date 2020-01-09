@extends('layout.app')

@section('title', 'Tambah Aktivitas')

@section('content')

<!-- begin:: Content -->
<form id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
    <input type="hidden" class="form-control" id="id" name="id">
    <input type="hidden" name="action" id="action" value="add">
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
                            <label>Kode Aktivitas</label>
                            <input type="text" class="form-control" name="kode_aktivitas" id="kode_aktivitas" placeholder="Kode Aktivitas maksimal 3 karakter" maxlength="3">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nama Aktivitas</label>
                            <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama aktivitas">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="kel mb2">
                            <span><em><strong>Produk atau Produk Rusak hanya dapat dipilih salah satu</strong></em></span>
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="radio" name="selector" value="1" id="selector_produk_stok"> Produk
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="produk_stok" id="produk_stok">
                                        <option value="">Pilih jenis</option>
                                        <option value="1">Mengurangi</option>
                                        <option value="2">Menambah</option>
                                        <option value="3">Menambah & Mengurangi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="radio" name="selector" value="2" id="selector_produk_rusak"> Produk rusak
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="produk_rusak" id="produk_rusak">
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
                            <div class="row">
                                <div class="col-4 col-form-label">
                                    <label class="kt-checkbox kt-checkbox--brand">
                                        <input type="checkbox" id="selector_pallet_rusak"> Pallet rusak
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control kt-selectpicker" name="pallet_rusak" id="pallet_rusak">
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
                                @if (!empty($aktivitas->upload_foto))
                                    @php 
                                        $show_upload_foto = 'display:block'; 
                                        $checked = 'checked'; 
                                    @endphp
                                @else
                                    @php 
                                        $show_upload_foto = 'display:none'; 
                                        $checked = ''; 
                                    @endphp
                                @endif
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" id="upload_foto" name="butuh_upload_foto" value="1" {{$checked}}> Upload Foto
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-3">
                                    <span id="upload_foto-label" onclick="showModalUploadFoto()" class="pull-right pointer kt-font-success kt-font-bold undelinehov" style="{{$show_upload_foto}}">Lihat</span>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="connect_sistro" id="connect_sistro" value="1"> Connect Sistro
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pengiriman" id="pengiriman" value="1"> Pengiriman GP
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" data-toggle="kt-tooltip" data-placement="top" title="Pengiriman GP atau Pengiriman Gudang Internal harus dipilih untuk dapat mengaktifkan fitur ini">
                                        <input type="checkbox" name="butuh_approval" id="butuh_approval" value="1"> Butuh Approval
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="peminjaman" id="peminjaman" value="1"> Peminjaman
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="penyusutan" id="penyusutan" value="1"> Penyusutan
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" data-toggle="kt-tooltip" data-placement="top" title="Tidak pengaruh tanggal produksi harus dicentang untuk dapat mengaktifkan fitur ini">
                                        <input type="checkbox" name="fifo" id="fifo" value="1"> FIFO
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pengaruh_tgl_produksi" id="pengaruh_tgl_produksi" value="1"> Tidak pengaruh tgl produksi
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="internal_gudang" id="internal_gudang" value="1"> Pengiriman Gudang Internal
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="kelayakan" id="kelayakan" value="1"> Kelayakan
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="penerimaan_gi" id="penerimaan_gi" value="1"> Penerimaan GI
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            {{-- <div class="row form-group mb-0 mb2">
                                <div class="col-6 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pindah_area" id="pindah_area" value="1"> Pindah Area
                                        <span></span>
                                    </label>
                                </div>
                            </div> --}}
                            
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="row form-group mb-0 mb2">
                                @if (!empty($aktivitas->butuh_alat_berat))
                                    @php 
                                        $show_alat_berat = 'display:block'; 
                                        $checked = 'checked'; 
                                    @endphp
                                @else
                                    @php 
                                        $show_alat_berat = 'display:none'; 
                                        $checked = ''; 
                                    @endphp
                                @endif
                                <div class="col-9 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_alat_berat" id="butuh_alat_berat" value="1" {{$checked}}> Butuh alat berat
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-2">
                                    <span id="butuh_alat_berat-label" onclick="showModalAlatBerat()" class="pull-right pointer kt-font-success kt-font-bold undelinehov" style="{{$show_alat_berat}}">Lihat</span>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                @if (!empty($anggaran_tkbm))
                                    @php 
                                        $show_anggaran_tkbm = 'display:block'; 
                                        $checked = 'checked'; 
                                    @endphp
                                @else
                                    @php 
                                        $show_anggaran_tkbm = 'display:none'; 
                                        $checked = ''; 
                                    @endphp
                                @endif
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_tkbm" id="butuh_tkbm" value="1"> Butuh TKBM
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-3">
                                    <span id="butuh_tkbm-label" onclick="showModalTkbm()" class="pull-right pointer kt-font-success kt-font-bold undelinehov" style="{{$show_anggaran_tkbm}}">Lihat</span>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="tanda_tangan" id="tanda_tangan" value="1"> Butuh Tanda Tangan
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="butuh_biaya" id="butuh_biaya" value="1"> Butuh Biaya Alat Berat
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row form-group mb-0 mb2">
                                <div class="col-12 offset-col-2">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="so" id="so" value="1"> SO
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
                            <input type="text" class="form-control" id="start_date" name="start_date" readonly placeholder="Pilih tanggal" value="{{date('d-m-Y')}}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="text" class="form-control" id="end_date" name="end_date" readonly placeholder="Pilih tanggal">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalFoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Upload Foto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb1">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" id="select_all_photos"> Pilih Semua
                                        <span></span>
                                    </label>
                                </div>
                                @foreach ($foto as $row)
                                    <div class="col-md-4">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input class="upload_foto_checkbox" type="checkbox" name="upload_foto[]" id="upload_foto_{{$row->id}}" value="{{$row->id}}"> {{$row->nama}}
                                            <span></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalAlatBerat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Alat berat</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                        <input type="checkbox" name="pilih_semua" id="select_all_alat_berat" value="1"> Pilih Semua
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="kel mt1" style="padding: .5rem !important">
                                        <div class="col-12 mb1">
                                            <small >pilih spesifik</small>
                                        </div>
                                        @php $i=0 @endphp
                                        @foreach ($alat_berat as $row)
                                            <div class="col-md-12">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input class="alat_berat_checkbox" type="checkbox" onclick="loadAnggaran({{$row->id}})" name="alat_berat[]" id="alat_berat_{{$row->id}}" value="{{$row->id}}"> {{$row->nama}}
                                                    <span></span>
                                                </label>
                                                <div id="tempat_anggaran_{{$row->id}}" class="form-group alat_berat" style="display:none">
                                                    <small for="idAForklift">Masukkan Anggaran</small>
                                                    <input type="text" class="form-control anggaran" name="anggaran[{{$row->id}}]" id="anggaran_{{$row->id}}" placeholder="Masukkan Anggaran">
                                                </div>
                                            </div>
                                        @php $i++ @endphp
                                        @endforeach
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalTkbm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Anggaran TKBM</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="tempat_anggaran" class="form-group">
                                        <small for="idAForklift">Masukkan Anggaran</small>
                                        <input type="text" class="form-control anggaran" name="anggaran_tkbm" id="anggaran_tkbm" value="{{$anggaran_tkbm}}" placeholder="Masukkan Anggaran TKBM">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

        $("#produk_rusak").attr('disabled',true);
        $("#produk_rusak").selectpicker('refresh');

        $("#pallet_stok").attr('disabled',true);
        $("#pallet_stok").selectpicker('refresh');

        $("#pallet_dipakai").attr('disabled',true);
        $("#pallet_dipakai").selectpicker('refresh');

        $("#pallet_kosong").attr('disabled',true);
        $("#pallet_kosong").selectpicker('refresh');

        $("#pallet_rusak").attr('disabled',true);
        $("#pallet_rusak").selectpicker('refresh');
        
        if ($('#internal_gudang').checked || $('#pengiriman').checked) {
            $("#butuh_approval").attr('disabled', false);
        } else {
            $("#butuh_approval").attr('disabled', true);
        }

        // $("#fifo").attr('disabled',true);

        $("#pengaruh_tgl_produksi").prop('checked', true);

        @if (!empty($id)) {
            edit({{$id}});
        }
        @endif
    });
    $('.kt-selectpicker').selectpicker();
    $('#end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format:'dd-mm-yyyy',
        clearBtn:true,
        orientation: "top left"
    });

    $('input[type=radio][name=selector]').change(function() {
        if (this.value == 1) {
            $("#produk_stok").attr('disabled',false);
            $("#produk_stok").selectpicker('refresh');
            $("#produk_rusak").val("");
            $("#produk_rusak").attr('disabled',true);
            $("#produk_rusak").selectpicker('refresh');
        } else if (this.value == 2) {
            $("#produk_rusak").attr('disabled',false);
            $("#produk_rusak").selectpicker('refresh');
            $("#produk_stok").val("");
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

    $("#selector_pallet_rusak").change(function() {
        if(this.checked) {
            $("#pallet_rusak").attr('disabled',false);
            $("#pallet_rusak").selectpicker('refresh');
        } else {
            $("#pallet_rusak").val("").change();
            $("#pallet_rusak").attr('disabled',true);
            $("#pallet_rusak").selectpicker('refresh');
        }
    });

    $('#upload_foto').on('change', function(e){
        if(e.target.checked){
            $('#modalFoto').modal();
            $('.upload_foto_checkbox').not(this).prop('checked', false);
            $('#upload_foto-label').show('slow');
        } else {
            $('#upload_foto-label').hide();
        }
    });
    $("#select_all_photos").click(function(){
        $('.upload_foto_checkbox').not(this).prop('checked', this.checked);
    });
    $("#select_all_alat_berat").click(function(e){
        if(e.target.checked){
            $('.alat_berat_checkbox').not(this).prop('checked', this.checked);
            $('.alat_berat').show('slow');
        } else {
            $('.alat_berat_checkbox').not(this).prop('checked', false);
            $('.alat_berat').hide('slow');
        }
    });

    function showModalUploadFoto(){
        $('#modalFoto').modal();
        const id = $("#id").val();
        $.ajax({
            url: baseUrl+"master-aktivitas/get-upload-foto/"+id,
            success:res=>{
                const obj = res.data;
                obj.forEach(element => {
                    $("#upload_foto_"+element.id_foto_jenis).attr('checked', true);
                });
            },
            error:()=>{

            }
        });
    }


    $('#butuh_alat_berat').on('change', function(e){
        if(e.target.checked){
            $('#modalAlatBerat').modal();
            $('.alat_berat_checkbox').not(this).prop('checked', false);
            $('.alat_berat').hide();
            $('#butuh_alat_berat-label').show('slow');
        } else {
            $('#butuh_alat_berat-label').hide();
        }
    });

    $('#butuh_tkbm').on('change', function(e){
        if(e.target.checked){
            $('#modalTkbm').modal();
            $('#butuh_tkbm-label').show('slow');
        } else {
            $('#anggaran_tkbm').val('');
            $('#butuh_tkbm-label').hide();
        }
    });

    function showModalAlatBerat(){
        $('#modalAlatBerat').modal();
        const id = $("#id").val();
        $.ajax({
            url: baseUrl+"master-aktivitas/get-alat-berat/"+id,
            success:res=>{
                const obj = res.data;
                obj.forEach(element => {
                    $("#alat_berat_"+element.id_kategori_alat_berat).attr('checked', true);
                    $("#tempat_anggaran_"+element.id_kategori_alat_berat).show();
                    $("#anggaran_"+element.id_kategori_alat_berat).val(element.anggaran);
                });
            },
            error:()=>{

            }
        });
    }

    function showModalTkbm(){
        $('#modalTkbm').modal();
        // const id = $("#id").val();
        
    }

    function loadAnggaran(id) {
        $('#alat_berat_'+id).on('change', function(e){
            if(e.target.checked){
                $('#tempat_anggaran_'+id).show('slow');
            } else {
                $('#tempat_anggaran_'+id).hide('slow');
            }
        });
    }

    $('#pengiriman').on('change', function(e){
        if(e.target.checked){
            $('#internal_gudang').prop('checked', false);
            $('#internal_gudang').attr('disabled',true);
            $('#butuh_approval').attr('disabled',false);
        } else {
            $('#internal_gudang').attr('disabled', false);

            if ($("#internal_gudang").checked || $("#pengiriman").checked) {
                $("#butuh_approval").attr("disabled", false);
            } else {
                $("#butuh_approval").prop("checked", false);
                $("#butuh_approval").attr("disabled", true);
            }
        }
    });

    $('#pengaruh_tgl_produksi').on('change', function(e){
        if(e.target.checked){
            $('#fifo').attr('disabled',false);
        } else {
            $('#fifo').prop('checked',false);
            $('#fifo').attr('disabled',true);
        }
    });

    $('#internal_gudang').on('change', function(e){
        // console.log(e);
        // console.log($('#internal_gudang'));
        if(e.target.checked){
            $('#pengiriman').prop('checked', false);
            $('#pengiriman').attr('disabled',true);
            $('#butuh_approval').attr('disabled',false);
        } else {
            $('#pengiriman').attr('disabled', false);

            if ($("#internal_gudang").checked || $("#pengiriman").checked) {
                $("#butuh_approval").attr("disabled", false);
            } else {
                $("#butuh_approval").prop("checked", false);
                $("#butuh_approval").attr("disabled", true);
            }
        }
    });

</script>
@endsection