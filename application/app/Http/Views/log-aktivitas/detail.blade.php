@extends('layout.app')

@section('title', 'Detail Log Aktivitas')

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
                    Detail Log Aktivitas
                </h4>
                <p class="sub">
                    Berikut ini adalah detail Log Aktivitas <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <a href="{{url('log-aktivitas')}}" class="pointer"><span class="pull-right color-dodolo"><i class="la la-arrow-left"></i>kembali</span></a>
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
                        <label>Gudang Tujuan</label>
                        <h5 class="boldd"> {{$aktivitasHarian->gudangTujuan->nama??'-'}}</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Paket Alat Berat</label>
                        @php $no = 1; @endphp
                        <h5 class="boldd"> 
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
                        <label>Checker</label>
                        <h5 class="boldd"> {{$aktivitasHarian->checker->checker->nama??'-'}}</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Karu</label>
                        <h5 class="boldd"> {{$aktivitasHarian->karu->karu->nama??'-'}}</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Tanggal</label>
                        <h5 class="boldd"> {{date('d-m-Y', strtotime($aktivitasHarian->updated_at))}}</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Jam</label>
                        <h5 class="boldd"> {{date('H:i', strtotime($aktivitasHarian->updated_at))}}</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Foto Truk</label><br>
                        <a href="#" class="boldd color-green"  data-toggle="modal"
                        data-target="#kt_modal_2"> Lihat Foto</a>
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
                                    {{$item->material->nama??'-'}} - <span class="boldd">{{$item->jumlah}} Ton</span>
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

    @if ($aktivitasHarian->aktivitas->so != null || $aktivitasHarian->aktivitas->pengiriman != null)
    <div class="kt-portlet__foot">
        <div class="kt-form__actions">
            <div class="row">
                <div class="col-10">
                    <a href="{{url('log-aktivitas/cetak-aktivitas/'.$aktivitasHarian->id)}}" type="button" class="btn btn-success btn-lg" target="_blank" > <i class="fa fa-print"></i> Cetak</a>
                </div>
            </div>
        </div>
    </div>
    @endif
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
                                    @if (file_exists(storage_path("/app/public/kelayakan/" . $item->id_aktivitas_harian . "/" . $aktivitasHarian->file_enc)))
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
                        <span><strong>Tidak ada foto</strong></span>
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

<!--begin::Modal-->
<div class="modal fade" id="kt_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Foto Log Aktivitas</h5>
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

<script type="text/javascript">
    const id_gudang = "{{$id_gudang}}";
    const id_aktivitas_harian = "{{$id_aktivitas_harian}}";

    let ajaxUrl = baseUrl + "log-aktivitas",
    ajaxSource = ajaxUrl;

    $(".fancybox").fancybox({
        openEffect: "none",
        helpers   : { 
            overlay : null
        },
        closeEffect: "none"
    });

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
                                <i class="flaticon2-shelter"></i> Area ${element.area_stok.area.nama}
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

</script>

@stop