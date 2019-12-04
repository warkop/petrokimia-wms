@extends('layout.app')

@section('title', 'Detail Aktivitas')

@section('content')

<link rel="stylesheet" href="{{asset('assets/extends/plugin/fancybox-simple/jquery.fancybox.min.css')}}">


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Detail Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah detail aktivitas <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <a href="{{url('penerimaan-gp')}}" class="pointer"><span class="pull-right color-dodolo"><i
                            class="la la-arrow-left"></i>kembali</span></a>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-6">
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Jenis Aktivitas</label>
                            <h5 class="boldd"> {{$aktivitasHarian->aktivitas->nama}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Shift Kerja</label>
                            <h5 class="boldd"> {{$aktivitasHarian->shift->nama??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Gudang Tujuan</label>
                            <h5 class="boldd"> {{$aktivitasHarian->gudangTujuan->nama??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Paket Alat Berat</label>
                            <h5 class="boldd"> {{$$aktivitasHarian->alatBerat->nomor_lambung??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Sistro</label>
                            <h5 class="boldd"> {{$aktivitasHarian->sistro??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Gambar</label><br>
                            <a href="#" class="boldd color-green" data-toggle="modal" data-target="#kt_modal_2"> Lihat
                                Gambar</a>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="row listproduk">
                        <label class="boldd mb1">List Produk</label>
                        <div class="kt-widget4 col-12 kel">
                            @php 
                            $total = 0;
                            $temp_material = '';
                            @endphp
                            @foreach ($produk as $item)
                            <div class="kt-widget4__item border-bottom-dash">
                                <div class="kt-widget4__info">

                                        {{-- @if ($temp_material != $item->material->nama) --}}
                                        @php 
                                            // $total += $item->jumlah;
                                        @endphp
                                        <p class="kt-widget4__username">
                                            {{$item->material->nama??'-'}} - <span class="boldd">{{$total}} Ton</span>
                                        </p>
                                        @php
                                            // $temp_material = $item->material->nama
                                        @endphp
                                        {{-- @endif --}}

                                        <p class="kt-widget4__text color-oren boldd">
                                            {{$item->text_tipe}}
                                        </p>
                                </div>
                                    <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                    data-target="#kt_modal" onclick="loadArea({{$item->id_gudang_stok}})">Area</a>
                                </div>
                                    @endforeach
                                <div class="border-pembatas mb1"></div>
                            {{-- <div class="kt-widget4__item border-bottom-dash ">
                                <div class="kt-widget4__info">
                                    <p class="kt-widget4__username">
                                        Pupuk ZE - <span class="boldd">20 Ton</span>
                                    </p>
                                    <p class="kt-widget4__text color-oren boldd">
                                        Menambah
                                    </p>
                                </div>
                                <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                    data-target="#kt_modal">Area</a>
                            </div> --}}
                            
                        </div>
                    </div>
                    <div class="row listterplas mt2">
                        <label class="boldd mb1">List Pallet</label>
                        <div class="kt-widget4 col-12 kel">
                            <div class="kt-widget4__item border-bottom-dash">
                                <div class="kt-widget4__info">
                                    <p class="kt-widget4__username">
                                        Terplas - <span class="boldd">100</span>
                                    </p>
                                    <p class="kt-widget4__text color-green boldd">
                                        Menambah
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget4__item border-bottom-dash">
                                <div class="kt-widget4__info">
                                    <p class="kt-widget4__username">
                                        Terplas - <span class="boldd">20</span>
                                    </p>
                                    <p class="kt-widget4__text color-green boldd">
                                        Menambah
                                    </p>
                                </div>
                            </div>
                            <div class="border-pembatas mb1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-10">
                        @if ($aktivitasHarian->approve == null)
                        <button type="button" class="btn btn-wms btn-lg" onclick="approve()">Approve</button>
                        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
                            data-target="#kt_keluhan" onclick="loadKeluhan()">Keluhan</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->



<!--begin::Modal-->
<div class="modal fade" id="kt_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <label class="boldd">List Area</label>
                    <!--begin::Accordion-->
                    <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample5">
                        <div class="card">
                            <div class="card-header" id="headingOne5">
                                <div class="card-title" data-toggle="collapse" data-target="#collapseOne5"
                                    aria-expanded="true" aria-controls="collapseOne5">
                                    <i class="flaticon2-shelter"></i> Area A
                                </div>
                            </div>
                            <div id="collapseOne5" class="collapse" aria-labelledby="headingOne5"
                                data-parent="#accordionExample5">
                                <div class="card-body">
                                    <div class="kt-widget4__item border-bottom-dash mt1">
                                        <div class="kt-widget4__info">
                                            <h6 class="kt-widget4__username">
                                                20 Agustus 2019
                                            </h6>
                                            <p class="kt-widget4__text boldd">
                                                20 Ton
                                            </p>
                                        </div>
                                    </div>
                                    <div class="kt-widget4__item border-bottom-dash mt1">
                                        <div class="kt-widget4__info">
                                            <h6 class="kt-widget4__username">
                                                20 Agustus 2019
                                            </h6>
                                            <p class="kt-widget4__text boldd">
                                                10 Ton
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingTwo5">
                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo5"
                                    aria-expanded="false" aria-controls="collapseTwo5">
                                    <i class="flaticon2-shelter"></i> Area B
                                </div>
                            </div>
                            <div id="collapseTwo5" class="collapse" aria-labelledby="headingTwo5"
                                data-parent="#accordionExample5">
                                <div class="card-body">
                                    <div class="kt-widget4__item border-bottom-dash mt1">
                                        <div class="kt-widget4__info">
                                            <h6 class="kt-widget4__username">
                                                20 Agustus 2019
                                            </h6>
                                            <p class="kt-widget4__text boldd">
                                                30 Ton
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingThree5">
                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree5"
                                    aria-expanded="false" aria-controls="collapseThree5">
                                    <i class="flaticon2-shelter"></i> Area C
                                </div>
                            </div>
                            <div id="collapseThree5" class="collapse" aria-labelledby="headingThree5"
                                data-parent="#accordionExample5">
                                <div class="card-body">
                                    <div class="kt-widget4__item border-bottom-dash mt1">
                                        <div class="kt-widget4__info">
                                            <h6 class="kt-widget4__username">
                                                20 Agustus 2019
                                            </h6>
                                            <p class="kt-widget4__text boldd">
                                                10 Ton
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end::Accordion-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->


<!--begin::Modal-->
<div class="modal fade" id="kt_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Foto Aktivitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="kt-scroll" data-scroll="true" data-height="400">
                        <div class="row mb2">
                            @foreach ($aktivitasFoto as $item)
                                <div class="col-4">
                                    <label class="boldd">Foto {{$item->fotoJenis->nama}}</label>
                                    <a class="fancybox" rel="ligthbox"
                                        href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}">
                                        <img class="img-fluid"
                                            src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}" alt=""
                                            srcset="">
                                    </a>
                                </div>
                            @endforeach
                            {{-- <div class="col-4">
                                <label class="boldd">Foto Bawah</label>
                                <a class="fancybox" rel="ligthbox"
                                    href="{{asset('assets/main/metronic/media/products/product2.jpg')}}">
                                    <img class="img-fluid"
                                        src="{{asset('assets/main/metronic/media/products/product2.jpg')}}" alt=""
                                        srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Depan</label>
                                <a class="fancybox" rel="ligthbox"
                                    href="{{asset('assets/main/metronic/media/products/product4.jpg')}}">
                                    <img class="img-fluid"
                                        src="{{asset('assets/main/metronic/media/products/product4.jpg')}}" alt=""
                                        srcset="">
                                </a>
                            </div> --}}
                        </div>
                        {{-- <div class="row mb2">
                            <div class="col-4">
                                <label class="boldd">Foto Belakang</label>
                                <a class="fancybox" rel="ligthbox"
                                    href="{{asset('assets/main/metronic/media/products/product5.jpg')}}">
                                    <img class="img-fluid"
                                        src="{{asset('assets/main/metronic/media/products/product5.jpg')}}" alt=""
                                        srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kanan</label>
                                <a class="fancybox" rel="ligthbox"
                                    href="{{asset('assets/main/metronic/media/products/product6.jpg')}}">
                                    <img class="img-fluid"
                                        src="{{asset('assets/main/metronic/media/products/product6.jpg')}}" alt=""
                                        srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kiri</label>
                                <a class="fancybox" rel="ligthbox"
                                    href="{{asset('assets/main/metronic/media/products/product6.jpg')}}">
                                    <img class="img-fluid"
                                        src="{{asset('assets/main/metronic/media/products/product6.jpg')}}" alt=""
                                        srcset="">
                                </a>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<div class="modal fade" id="kt_keluhan" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Keluhan Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="form1">
            <div class="modal-body">
                <div class="row mb2">
                    <div class="col-8">
                        <h5 class="boldd">List Produk</h5>
                    </div>
                    <div class="col-4">
                        <p class="btn btn-outline-success pull-right cursor pointer" onclick="tambah()"><i class="la la-plus"></i>
                            Tambah</p>
                    </div>
                </div>
                <div id="table_produk" style="border-bottom: 2px solid #F2F3F8">
                    <div id="belumada" class="row kel">
                        <div class="belum col-12 text-center">
                            <label class="boldd dashed">Belum ada daftar produk</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary ladda-button" data-style="zoom-in" id="btn_save">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>


<script src="{{asset('assets/extends/plugin/fancybox-simple/jquery.fancybox.min.js')}}"></script>
<script type="text/javascript">
let datatable,
    tableTarget = "#kt_table_1",
    ajaxUrl = baseUrl + "penerimaan-gp",
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;
    const id_aktivitas_harian = "{{$aktivitasHarian->id}}";

    $(document).ready(()=>{
        $("#btn_save").on("click", function(e) {
            e.preventDefault();
            laddaButton = Ladda.create(this);
            laddaButton.start();
            simpan();
        });
    })


    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });

    function tambah(obj='') {
        const tableId = "table_produk";
        const rows = document.getElementById(tableId).getElementsByTagName("div").length;
        $("#table_produk").append(`
        <div class="row mb2 produk_baris" id="baris-produk-${rows}">
            <div class="col-3">
                <label class="boldd-500">Pilih Produk</label>
                <select class="form-control select2Custom m-select2" id="produk-${rows}" name="produk[]" aria-placeholder="Pilih Produk" style="width: 100%;">
                    <option disabled selected>Pilih Produk</option>
                </select>
            </div>
            <div class="col-2">
                <label class="boldd-500">Jumlah</label><br>
                <input type="text" id="jumlah-${rows}" name="jumlah[]" class="form-control" placeholder="Jumlah">
            </div>
            <div class="col-5">
                <label class="boldd-500">Keluhan</label>
                <textarea class="form-control" id="keluhan-${rows}" name="keluhan[]" rows="2"></textarea>
            </div>
            <div class="col-2">
                <label class="visibility-hide">Area</label><br>
                <button href="javascript:void(0)" type="button" class="btn btn-danger cursor pointer btn-elevate btn-icon button_hapus" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
            </div>
        </div>
        `);

        loadProduk(rows, `#produk-${rows}`, obj)
        $('.select2Custom').select2({
            placeholder: "Pilih Produk",
            dropdownParent:$("#kt_keluhan")
        });
        
    }

    $("body").on('click', '.button_hapus', function (e) {
        $(this).parent().parent().remove();
    });

    function loadProduk(no, target, produk='') {
        $.ajax({
            url:  baseUrl + "penerimaan-gp" + "/" + "get-produk/"+id_aktivitas_harian,
            success: res => {
                const obj = res.data;
                console.log(obj);
                let html = `<option value="">Pilih Produk</option>`;
                obj.forEach((item, index) => {
                    html += `<option value="${item.id}">${item.nama}</option>`;
                });

                $(target).html(html);
                $("#produk-"+no).val(produk.id_material);
                $("#jumlah-"+no).val(produk.jumlah);
                $("#keluhan-"+no).html(produk.keluhan);
            },
            error: () => {}
        });
    }

    function simpan() {
        let data = $("#form1").serializeArray();
        
        $.ajax({
            type: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: ajaxUrl + "/" + id_aktivitas_harian,
            data: data,
            beforeSend: function () {
                preventLeaving();
                $('.btn_close_modal').addClass('hide');
                $('.se-pre-con').show();
            },
            success: function (response) {
                laddaButton.stop();
                window.onbeforeunload = false;
                $('.btn_close_modal').removeClass('hide');
                $('.se-pre-con').hide();

                let obj = response;

                $('#kt_keluhan').modal('hide');
                swal.fire('Ok', obj.message, 'success');
            },
            error: function (response) {
                $("#btn_save").prop("disabled", false);
                let head = 'Maaf',
                    message = 'Terjadi kesalahan koneksi',
                    type = 'error';
                laddaButton.stop();
                window.onbeforeunload = false;
                $('.btn_close_modal').removeClass('hide');
                $('.se-pre-con').hide();

                if (response['status'] == 401 || response['status'] == 419) {
                    location.reload();
                } else {
                    if (response['status'] != 404 && response['status'] != 500) {
                        let obj = JSON.parse(response['responseText']);

                        if (!$.isEmptyObject(obj.message)) {
                            if (obj.code > 450) {
                                head = 'Maaf';
                                message = obj.message;
                                type = 'error';
                            } else {
                                head = 'Pemberitahuan';
                                type = 'warning';

                                obj = response.responseJSON.errors;
                                laddaButton.stop();
                                window.onbeforeunload = false;
                                $('.btn_close_modal').removeClass('hide');
                                $('.se-pre-con').hide();

                                const temp = Object.values(obj);
                                message = '';
                                temp.forEach(element => {
                                    element.forEach(row => {
                                        message += row + "<br>"
                                    });
                                });
                            }
                        }
                    }

                    swal.fire(head, message, type);
                }
            }
        });
    }

    function loadKeluhan(){
        $.ajax({
            url: "{{ url('penerimaan-gp') }}/get-produk/"+id_aktivitas_harian,
            success:res=>{
                
                if (res.keluhan !== '') {
                    $("#table_produk").html('');
                    const obj = res.keluhan;
                    obj.forEach(element => {
                        tambah(element)
                    });
                } else {
                    document.getElementById("belumada").remove();
                }
            },
            error:()=>{

            }
        });
    }

    function approve() {
         swal.fire({
            title: 'Apakah Anda yakin ingin menyetujui keluhan ini?',
            text: "Data yang sudah disetujui tidak bisa dibatalkan.",
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Tidak',
            confirmButtonText: 'Ya!'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('penerimaan-gp') }}/"+id_aktivitas_harian,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method:"PATCH",
                    success:res=>{
                        swal.fire('Ok', "Data berhasil disimpan", 'success').then(()=>{
                            location.href = ajaxSource;
                        });
                    },
                    error:(response)=>{
                        $("#btn_save").prop("disabled", false);
                        let head = 'Maaf',
                            message = 'Terjadi kesalahan koneksi',
                            type = 'error';
                        // laddaButton.stop();
                        window.onbeforeunload = false;
                        $('.btn_close_modal').removeClass('hide');
                        $('.se-pre-con').hide();

                        if (response['status'] == 401 || response['status'] == 419) {
                            location.reload();
                        } else {
                            if (response['status'] != 404 && response['status'] != 500) {
                                let obj = JSON.parse(response['responseText']);

                                if (!$.isEmptyObject(obj.message)) {
                                    if (obj.code > 450) {
                                        head = 'Maaf';
                                        message = obj.message;
                                        type = 'error';
                                    } else {
                                        head = 'Pemberitahuan';
                                        type = 'warning';
                                        obj = response.responseJSON.errors;
                                        message = '';
                                        // console.log(obj)
                                        if (obj == null) {
                                            message = response.responseJSON.message;
                                        } else {
                                            const temp = Object.values(obj);
                                            
                                            temp.forEach(element => {
                                                element.forEach(row => {
                                                    message += row + "<br>"
                                                });
                                            });
                                        }

                                        // laddaButton.stop();
                                        window.onbeforeunload = false;
                                        $('.btn_close_modal').removeClass('hide');
                                        $('.se-pre-con').hide();
                                        

                                        
                                    }
                                }
                            }

                            swal.fire(head, message, type);
                        }
                    }
                });
            }
        });
    }
</script>

@stop