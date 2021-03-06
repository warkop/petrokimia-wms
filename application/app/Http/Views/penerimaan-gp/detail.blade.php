@extends('layout.app')

@section('title', 'Detail Aktivitas')

@section('content')

<style>
    .br {
    border-radius: 8px;  
    }
    .w80 {
    width: 80%;
    }
    .card {
    border: 2px solid #fff;
    box-shadow:0px 0px 10px 0 #a9a9a9;
    padding: 30px 40px;
    width: 80%;
    margin: 50px auto;
    }
    .wrapper {
    width: 0px;
    animation: fullView 0.5s forwards cubic-bezier(0.250, 0.460, 0.450, 0.940);
    }
    .profilePic {
    height: 65px;
    width: 65px;
    border-radius: 50%;
    }
    .comment {
    height: 10px;
    background: #777;
    margin-top: 20px;
    }

    @keyframes fullView {
    100% {
        width: 100%;
    }
    }


    .animate {
    animation : shimmer 2s infinite linear;
    background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        background-size: 1000px 100%;
    }

    @keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
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
                            <h5 class="boldd"> {{$aktivitasHarian->aktivitas->nama??'-'}}</h5>
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
                            <label>Gudang Tujuan / Distributor</label>
                            <h5 class="boldd"> {{$aktivitasHarian->gudangTujuan->nama??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Paket Alat Berat</label>
                            <h5 class="boldd"> 
                                @php $no = 1; @endphp
                                @foreach ($aktivitasHarian->aktivitasHarianAlatBerat as $key)
                                {{ $no.'. '.$key->nomor_lambung}} <br>
                                @php $no++ @endphp
                                @endforeach
                            </h5>
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
                            <label>Nopol</label>
                            <h5 class="boldd"> {{$aktivitasHarian->nopol??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Driver</label>
                            <h5 class="boldd"> {{$aktivitasHarian->driver??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>No. SO / Posto</label>
                            <h5 class="boldd"> {{$aktivitasHarian->posto??'-'}}</h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Foto Truk</label><br>
                            <a href="#" class="boldd color-green" data-toggle="modal" data-target="#kt_modal_2"> Lihat Foto</a>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Foto Kelayakan</label><br>
                            <a href="#" class="boldd color-green" data-toggle="modal" data-target="#kt_modal_kelayakan"> Lihat Foto</a>
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
                                    <p class="kt-widget4__username">
                                        {{$item->nama_material??'-'}} - <span class="boldd">{{$item->jumlah}} Ton</span>
                                    </p>
                                     @if ($item->tipe == 1)
                                        <p class="kt-widget4__text color-oren boldd">
                                            Mengurangi    
                                        </p>
                                    @else 
                                        <p class="kt-widget4__text color-green boldd">
                                            Menambah
                                        </p>
                                    @endif
                                </div>
                                <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                data-target="#kt_modal" onclick="loadArea({{$item->id_material}})">Area</a>
                            </div>
                            @endforeach
                            <div class="border-pembatas mb1"></div>
                        </div>
                    </div>
                    <div class="row listterplas mt2">
                        <label class="boldd mb1">List Pallet</label>
                        <div class="kt-widget4 col-12 kel">
                            @foreach ($pallet as $item)
                                <div class="kt-widget4__item border-bottom-dash">
                                    <div class="kt-widget4__info">
                                        <p class="kt-widget4__username">
                                            {{$item->material->nama}} - <span class="boldd">{{$item->jumlah}} (
                                        @if ($item->status_pallet == 1) 
                                            {{ 'Pallet Stok' }} 
                                        @elseif ($item->status_pallet == 2) 
                                            {{ 'Pallet Terpakai' }} 
                                        @elseif ($item->status_pallet == 3) 
                                            {{ 'Pallet Kosong' }} 
                                        @elseif ($item->status_pallet == 4) 
                                            {{ 'Pallet Rusak' }} 
                                        @endif
                                        )</span>
                                        </p>
                                        @if ($item->tipe == 1)
                                            <p class="kt-widget4__text color-oren boldd">
                                                Mengurangi    
                                            </p>
                                        @else 
                                            <p class="kt-widget4__text color-green boldd">
                                                Menambah
                                            </p>
                                        @endif
                                    </div>
                                </div>    
                            @endforeach
                            <div class="border-pembatas mb1"></div>
                        </div>
                        <div class="row mb1">
                        <div class="col-12">
                            <label>Tanda Tangan</label><br>
                            @if (file_exists(storage_path("/app/public/aktivitas_harian/" . $aktivitasHarian->id . "/" . $aktivitasHarian->ttd)))
                                <a class="fancybox" rel="ligthbox"
                                    href="{{url('watch').'/'.$aktivitasHarian->ttd.'?un='.$aktivitasHarian->id.'&ctg=aktivitas_harian&src='.$aktivitasHarian->ttd}}">
                                    <img class="img-fluid"
                                        src="{{url('watch').'/'.$aktivitasHarian->ttd.'?un='.$aktivitasHarian->id.'&ctg=aktivitas_harian&src='.$aktivitasHarian->ttd}}" alt=""
                                        srcset="">
                                </a>
                            @else
                                <span class="kt-link kt-link--brand kt-font-bolder"><strong>File Tidak ada di server</strong></span>
                            @endif
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
                        @empty($aktivitasHarian->approve)
                        <button type="button" class="btn btn-wms btn-lg" onclick="approve()">Approve</button>
                        @endempty
                        <button type="button" class="btn btn-primary btn-lg" onclick="@if($aktivitasHarian->approve == null)loadKeluhan()@else loadDetail()@endif">Keluhan</button>
                        <a href="{{url('penerimaan-gp/cetak-aktivitas/'.$aktivitasHarian->id)}}" type="button" class="btn btn-success btn-lg" target="_blank" > <i class="fa fa-print"></i> Cetak</a>
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
                    <div class="accordion accordion-light  accordion-toggle-arrow" id="tempat_card">
                        
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
                                @if ($item->fotoJenis)
                                <div class="col-4">
                                    <label class="boldd">Foto {{$item->fotoJenis->nama}}</label>
                                    @if (file_exists(storage_path("/app/public/aktivitas_harian/" . $item->id_aktivitas_harian . "/" . $item->foto)))
                                        <a class="fancybox" rel="ligthbox"
                                            href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}">
                                            <img class="img-fluid"
                                                src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}" alt=""
                                                srcset="">
                                        </a>
                                    @else
                                        <span class="kt-link kt-link--brand kt-font-bolder"><strong>File Tidak ada di server</strong></span>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
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

<div class="modal fade" id="kt_modal_kelayakan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Foto Kelayakan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <label class="boldd">Kelayakan Sebelum</label>
                    </div>
                    <br>
                    <div class="kt-scroll" data-scroll="true">
                        <div class="row">
                            @if ($fotoKelayakanBefore->isEmpty())
                                <h4>Tidak ada foto</h4>
                            @endif
                            @foreach ($fotoKelayakanBefore as $item)
                                <div class="col-4">
                                    {{-- <label class="boldd">Foto {{$item->foto}}</label> --}}
                                    @if (file_exists(storage_path("/app/public/kelayakan/" . $item->id_aktivitas_harian . "/" . $item->file_enc)))
                                    <a class="fancybox" rel="ligthbox"
                                        href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}">
                                        <img class="img-fluid"
                                            src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}" alt=""
                                            srcset="">
                                    </a>
                                    @else
                                        <span class="kt-link kt-link--brand kt-font-bolder"><strong>File Tidak ada di server</strong></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <label class="boldd">Kelayakan Sesudah</label>
                    </div>
                    <br>
                    <div class="row">
                    @if ($fotoKelayakanAfter->isEmpty())
                        <h4>Tidak ada foto<h4>
                    @endif
                    </div>
                    <div class="kt-scroll" data-scroll="true">
                        <div class="row mb2">
                            {{-- @php dd($fotoKelayakanAfter->isEmpty()) @endphp --}}
                            @foreach ($fotoKelayakanAfter as $item)
                                <div class="col-4">
                                    {{-- <label class="boldd">Foto {{$item->foto}}</label> --}}
                                    @if (file_exists(storage_path("/app/public/kelayakan/" . $item->id_aktivitas_harian . "/" . $item->file_enc)))
                                    <a class="fancybox" rel="ligthbox"
                                        href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}">
                                        <img class="img-fluid"
                                            src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}" alt=""
                                            srcset="">
                                    </a>
                                    @else
                                        <span class="kt-link kt-link--brand kt-font-bolder"><strong>File Tidak ada di server</strong></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="kt_keluhan" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Keluhan Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <span class="kt-badge kt-badge--warning kt-badge--inline">Untuk angka desimal pemisahnya menggunakan simbol titik</span>
            <form id="form1" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb2">
                        <div class="col-8">
                            <h5 class="boldd">List Produk</h5>
                        </div>
                        @empty($aktivitasHarian->approve)
                        <div class="col-4">
                            <p class="btn btn-outline-success pull-right cursor pointer" onclick="tambah()"><i class="la la-plus"></i>
                                Tambah</p>

                        </div>
                        @endempty
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
                    @empty ($aktivitasHarian->approve)
                    <button type="submit" class="btn btn-primary ladda-button" data-style="zoom-in" id="btn_save">Simpan</button>
                    @endempty
                </div>
            </form>
        </div>
    </div>
</div>
<div id="image-adjs">
</div>

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
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">List keluhan</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama produk</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Keluhan</th>
                                    <th scope="col">Foto</th>
                                </tr>
                            </thead>
                            <tbody id="tubuh_produk">
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


<script type="text/javascript">
const id_gudang = "{{$aktivitasHarian->id_gudang}}";
const id_aktivitas_harian = "{{$aktivitasHarian->id}}";
const base_url = "{{url('watch')}}";

let datatable,
    tableTarget = "#kt_table_1",
    ajaxUrl = baseUrl + "penerimaan-gp",
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;

    $(document).ready(()=>{
        // $("#btn_save").on("click", function(e) {
        //     e.preventDefault();
        //     laddaButton = Ladda.create(this);
        //     laddaButton.start();
        //     simpan();
        // });
        
        $("#form1").on('submit',(function(e) {
        e.preventDefault();
        $.ajax({
                url: ajaxUrl + "/"+id_aktivitas_harian,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                beforeSend : function(){
                    preventLeaving();
                    $('.btn_close_modal').addClass('hide');
                    $('.se-pre-con').show();
                },
                success: function(response) {
                    window.onbeforeunload = false;
                    $('.btn_close_modal').removeClass('hide');
                    $('.se-pre-con').hide();
                    let obj = response;
                    $('#kt_keluhan').modal('hide');
                    swal.fire('Ok', obj.message, 'success');
                },
                error: function(response) {
                    
                    $("#btn_save").prop("disabled", false);
                    let head = 'Maaf',
                        message = 'Terjadi kesalahan koneksi',
                        type = 'error';
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
        }));

    })


    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });

    function tambah(obj='') {
        const tableId = "table_produk";
        const rows = document.getElementById(tableId).getElementsByTagName("div").length;
        @if ($aktivitasHarian->approve == null)
            $("#table_produk").append(`
            <div class="row mb2 produk_baris" id="baris-produk-${rows}">
                <div class="col-3">
                    <label class="boldd-500">Pilih Produk</label>
                    <select class="form-control select2Custom m-select2" id="produk-${rows}" name="produk[]" aria-placeholder="Pilih Produk" style="width: 100%;">
                        <option disabled selected>Pilih Produk</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="boldd-500">Jumlah (Ton)</label><br>
                    <input type="text" id="jumlah-${rows}" name="jumlah[]" class="form-control" placeholder="Jumlah">
                </div>
                <div class="col-3">
                    <label class="boldd-500">Upload File</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image-${rows}" name="foto[]" />
                        <label class="custom-file-label" for="customFile">Pilih file</label>
                    </div>
                </div>
                <div class="col-3">
                    <label class="boldd-500">Keluhan</label>
                    <textarea class="form-control" id="keluhan-${rows}" name="keluhan[]" rows="2"></textarea>
                </div>
                <div class="col-1">
                    <label class="visibility-hide">Area</label><br>
                    <button href="javascript:void(0)" type="button" class="btn btn-danger cursor pointer btn-elevate btn-icon button_hapus" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
                </div>
            </div>
            `);
            
            loadProduk(rows, `#produk-${rows}`, obj)
            $(`#produk-${rows}`).attr("readonly", false);
        @else
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
                    <input readonly type="text" id="jumlah-${rows}" name="jumlah[]" class="form-control" placeholder="Jumlah">
                </div>
                <div class="col-5">
                    <label class="boldd-500">Keluhan</label>
                    <textarea readonly class="form-control" id="keluhan-${rows}" name="keluhan[]" rows="2"></textarea>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button href="javascript:void(0)" type="button" class="btn btn-danger cursor pointer btn-elevate btn-icon button_hapus" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
                </div>
            </div>
            `);
            
            loadProduk(rows, `#produk-${rows}`, obj, false)

            // $(`#produk-${rows}`).attr("readonly", true);
        @endif
        $('.select2Custom').select2({
            placeholder: "Pilih Produk",
            dropdownParent:$(`#baris-produk-${rows}`)
        });
         
    }

    $("body").on('click', '.button_hapus', function (e) {
        $(this).parent().parent().remove();
    });

    function loadProduk(no, target, produk='', edit=true) {
        $.ajax({
            url:  baseUrl + "penerimaan-gp" + "/" + "get-produk/"+id_aktivitas_harian,
            beforeSend:()=>{
                $('.se-pre-con').show();
            },
            success: res => {
                $('.se-pre-con').hide();
                const obj = res.data;
                let html = `<option value="">Pilih Produk</option>`;
                obj.forEach((item, index) => {
                    html += `<option value="${item.id_material}">${item.nama}</option>`;
                });

                $(target).html(html);
                $("#produk-"+no).val(produk.id_material);
                $("#jumlah-"+no).val(produk.jumlah);
                $("#keluhan-"+no).html(produk.keluhan);

                if (edit == false) {
                    $("#produk-"+no).select2({
                    disabled: true
                    });
                }
            },
            error: (response) => {
                $('.se-pre-con').hide();
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

    function simpan() {
        $.ajax({
            type: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: ajaxUrl + "/" + id_aktivitas_harian,
            data: $("#form1").serializeArray(),
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
        $("#kt_keluhan").modal("show");
        $.ajax({
            url: "{{ url('penerimaan-gp') }}/get-produk/"+id_aktivitas_harian,
            success:res=>{
                
                if (res.keluhan != '') {
                    $("#table_produk").html('');
                    const obj = res.keluhan;
                    obj.forEach(element => {
                        tambah(element)
                    });
                } else {
                    document.getElementById("belumada").remove();
                }
            },
            error:response=>{
                let head = 'Maaf',
                    message = 'Terjadi kesalahan koneksi',
                    type = 'error';
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

    function approve() {
         swal.fire({
            title: 'Apakah Anda yakin?',
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

    function loadArea(id_material) {
        $.ajax({
            url:ajaxSource+'/get-area/'+id_gudang+"/"+id_material+"/"+id_aktivitas_harian,
            beforeSend:()=>{
                $("#tempat_card").html(`
                <div class="card br">
                    <div class="wrapper">
                        <div class="profilePic animate din"></div>
                        <div class="comment br animate w80"></div>
                        <div class="comment br animate"></div>
                        <div class="comment br animate"></div>
                    </div>
                <div>
                `);
            },
            success:(response) => {
                let tampung_nama = "";
                let temp_nama = "";
                let areanya = "";
                let temp = "";
                let i=1;
                response.forEach(element => {
                    temp_nama = `
                        <div class="card-header" id="heading-${i}">
                            <div class="card-title" data-toggle="collapse show" data-target="#collapse-${i}"
                                aria-expanded="true" aria-controls="collapse-${i}">
                                <i class="flaticon2-shelter"></i> Area ${element.nama_area}
                            </div>
                        </div>
                    `;

                    areanya = "";
                    areanya += `
                        <div class="kt-widget4__item border-bottom-dash mt1">
                            <div class="kt-widget4__info">
                                <h6 class="kt-widget4__username">
                                    ${helpDateFormat(element.tanggal, "mi")}
                                </h6>
                                <p class="kt-widget4__text boldd">
                                    ${element.jumlah} Ton
                                </p>
                            </div>
                        </div>`;
                    if (!$.isEmptyObject(temp_nama)) {
                        temp += `
                                <div class="card">
                                    <div id="collapse-${i}" class="collapse show" aria-labelledby="heading-${i}" data-parent="#tempat_card">
                                        <div class="card-body">
                                        ${temp_nama}
                                        ${areanya}
                                        </div>
                                    </div>
                                </div>`;
                    }
                    i++;
                });
                $("#tempat_card").html(temp);
            },
            error:response => {
                let head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
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
                                if (obj == null) {
                                    message = response.responseJSON.message;
                                } else {
                                    const temp = Object.values(obj);
                                    message = '';
                                    temp.forEach(element => {
                                        element.forEach(row => {
                                            message += row + "<br>"
                                        });
                                    });
                                }

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

function loadDetail() {
    $("#modal_detail").modal({backdrop: "static", keyboard: false},"show");

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/list-keluhan/"+ id_aktivitas_harian,
        beforeSend: function () {
        preventLeaving();
            $(".btn_close_modal").addClass("hide");
            $(".se-pre-con").show();
        },
        success: function (response) {
            window.onbeforeunload = false;
            $(".btn_close_modal").removeClass("hide");
            $(".se-pre-con").hide();

            let obj_produk = response.data;

            let text = "";
            let i=1;
            obj_produk.forEach(element => {
                text += `
                    <tr>
                        <td>${i}</td>
                        <td>${element.material.nama}</td>
                        <td>${element.jumlah}</td>
                        <td>${element.keluhan} Ton</td>
                        <td><image src="${base_url+'/'+element.foto+'?un='+element.id_aktivitas_harian+'&ctg=keluhan_gp&src='+element.foto}"></td>
                    </tr>
                `;
                i++;
            });
            $("#tubuh_produk").html(text);
        },
        error: function (response) {
            let head = "Maaf",
                message = "Terjadi kesalahan koneksi",
                type = "error";
            window.onbeforeunload = false;
            $(".btn_close_modal").removeClass("hide");
            $(".se-pre-con").hide();

            if (response["status"] == 401 || response["status"] == 419) {
                location.reload();
            } else {
                if (response["status"] != 404 && response["status"] != 500) {
                let obj = JSON.parse(response["responseText"]);

                if (!$.isEmptyObject(obj.message)) {
                    if (obj.code > 400) {
                    head = "Maaf";
                    message = obj.message;
                    type = "error";
                    } else {
                    head = "Pemberitahuan";
                    message = obj.message;
                    type = "warning";
                    }
                }
                }

                swal.fire(head, message, type);
            }
        }
    });
}
</script>

@stop