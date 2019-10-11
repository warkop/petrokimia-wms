@extends('layout.app')

@section('title', 'Tambah Aktivitas')

@section('content')




<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet" style="min-height: 70vh;">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Tambah Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah tambah aktivitas <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                {{-- <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal"
                        data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Aktivitas</a>
                </div> --}}
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row mb2">
                <div class="col-8">
                    <h5 class="boldd">List Produk</h5>
                </div>
                <div class="col-4">
                    <button href="#" class="btn btn-success btn-elevate btn-elevate-air pull-right" id="addBtnM"><i
                            class="la la-plus"></i> Tambah</button>
                </div>
            </div>
            <div id="inputAdjsts" style="border-bottom: 2px solid #F2F3F8">
                <div id="belumada" class="row kel">
                    <div class="belum col-12 text-center">
                        <label class="boldd dashed">Belum ada daftar produk</label>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-4">
                    <label>Pilih Produk</label>
                    <select class="form-control m-select2" id="kt_select2_1" name="param"
                        aria-placeholder="Pilih Area" style="width: 100%;">
                        <option value="">Pilih Area</option>
                        <option value="AK">Urea</option>
                        <option value="HI">ZA</option>
                    </select>
                </div>
                <div class="col-2">
                    <label>Jumlah</label><br>
                    <h5 class="col-2 col-form-label">10</h5>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Jumlah</label><br>
                    <h5 class="color-green col-2 col-form-label">menambah</h5>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button onclick="showArea()" type="button" class="btn btn-outline-success disabled">Pilih
                        Area</button>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body"
                        data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i
                            class="flaticon-delete"></i> </button>
                </div>
            </div> --}}


            <div class="row mb2 mt5">
                <div class="col-8">
                    <h5 class="boldd">List Pallet</h5>
                </div>
                <div class="col-4">
                    <button href="#" class="btn btn-success btn-elevate btn-elevate-air pull-right"
                        id="addBtnMPallet"><i class="la la-plus"></i> Tambah</button>
                </div>
            </div>
            <div id="inputAdjstPallet" class="mb5" style="border-bottom: 2px solid #F2F3F8">
                <div id="belumada2" class="row kel">
                    <div class="belum col-12 text-center">
                        <label class="boldd dashed">Belum ada daftar pallet</label>
                    </div>
                </div>
            </div>


            <div class="row ">
                <div class="col-12">
                    <h5 class="boldd">Tambah Foto</h5>
                </div>
                <div class="row mt1 mb2">
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Atas</h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Bawah
                                </h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Depan
                                </h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Belakang
                                </h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Kanan
                                </h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kt-dropzone dropzone dz-clickable" action="inc/api/dropzone/upload.php"
                            id="m-dropzone-one" style="background:#f5f6fa">
                            <div class="kt-dropzone__msg dz-message needsclick">
                                <h5 class="kt-dropzone__msg-title">Pilih Foto Tampak Kiri
                                </h5>
                                <span class="kt-dropzone__msg-desc">hanya diijinkan mengunggah
                                    <strong>satu</strong> foto.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button type="reset" class="btn-3 btn btn-success btn-lg">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->




<div class="modal fade" id="kt_modal_area" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row mb1">
                        <div class="col-6">
                            {{-- <h6 class="boldd">Pilih area yang tersedia untuk menampung</h6> --}}
                        </div>
                        <div class="col-6">
                            <h6 class="pull-right">Jumlah barang kamu : <span class="boldd">200</span></h6>
                        </div>
                    </div>
                    <div class="container kel">
                        <div class="row mb2">
                            <div class="col-6">
                                <h6 class="boldd col-form-label">Pilih area yang tersedia untuk menampung</h6>
                            </div>
                            <div class="col-6">
                                <p class="btn btn-success pull-right pointer"
                                    id="addBtnArea"><i class="la la-plus"></i> Tambah Area</p>
                            </div>
                        </div>
                        <div id="areaAdjst">
                                
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $('#area1').select2({
        placeholder: "Pilih Produk"
    });



    $('#kt_select2_1').select2({
        placeholder: "Pilih Produk"
    });

    function showArea() {
        $('#kt_modal_area').modal();
};


$("#addBtnM").click(function () {
        $("#inputAdjsts").append(`
        <div class="row mb2">
                <div class="col-4">
                    <label>Pilih Produk</label>
                    <select class="form-control m-select2" id="kt_select2_1" name="param"
                        aria-placeholder="Pilih Area" style="width: 100%;">
                        <option value="">Pilih Area</option>
                        <option value="AK">Urea</option>
                        <option value="HI">ZA</option>
                    </select>
                </div>
                <div class="col-2">
                    <label>Jumlah</label><br>
                    <h5 class="col-2 col-form-label">10</h5>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Jumlah</label><br>
                    <h5 class="color-green col-2 col-form-label">menambah</h5>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button onclick="showArea()" type="button" class="btn btn-outline-success disabled">Pilih
                        Area</button>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body"
                        data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i
                            class="flaticon-delete"></i> </button>
                </div>
            </div>
        `);

        document.getElementById("belumada").remove();
    });


    $("#addBtnMPallet").click(function () {
        $("#inputAdjstPallet").append(`
        <div class="row mb2">
                <div class="col-4">
                    <label>Pilih Produk</label>
                    <select class="form-control m-select2" id="kt_select2_1" name="param"
                        aria-placeholder="Pilih Area" style="width: 100%;">
                        <option value="">Pilih Area</option>
                        <option value="AK">Urea</option>
                        <option value="HI">ZA</option>
                    </select>
                </div>
                <div class="col-2">
                    <label>Jumlah</label><br>
                    <h5 class="col-2 col-form-label">10</h5>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Jumlah</label><br>
                    <h5 class="color-green col-2 col-form-label">menambah</h5>
                </div>
                <div class="col-2 offset-2">
                    <label class="visibility-hide">Area</label><br>
                    <button type="button" class="btn btn-danger btn-elevate btn-icon" data-container="body"
                        data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i
                            class="flaticon-delete"></i> </button>
                </div>
            </div>
        `);

        document.getElementById("belumada2").remove();
    });


    $("#addBtnArea").click(function () {
        $("#areaAdjst").append(`
        <div class="row mb1 pilihA">
            <div class="col-4">
                <label>Pilih Area</label>
                <select class="form-control m-select2" id="kt_select2_1">
                    <option value="">Pilih Area</option>
                    <option value="AK">Area 1</option>
                    <option value="HI">Area 2</option>
                </select>
            </div>
            <div class="col-4">
                <label>Masukkan Jumlah</label>
                <input type="text" class="form-control" aria-describedby="text" placeholder="Masukkan jumlah">
            </div>
            <div class="col-3">
                <label>Tersedia</label>
                <p class="col-form-label"><span class="boldd">50 Kapasaitas</span></p>
            </div>
            <div class="col-1">
            <label class="visibility-hide">hapus</label>
            <button type="button" class="btn btn-danger btn-elevate btn-icon" data-toggle="kt-tooltip" data-placement="top" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
            </div>
        </div>
        `);
    });


    

</script>

@stop