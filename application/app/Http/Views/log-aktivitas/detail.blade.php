@extends('layout.app')

@section('title', 'Detail Log Aktivitas')

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
                        @php $no = 1; @endphp
                        <h5 class="boldd"> @foreach ($aktivitasHarian->aktivitasHarianAlatBerat as $key)
                            {{ $no.'. '.$key->nomor_lambung}} <br>
                            @php $no++ @endphp
                        @endforeach</h5>
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
                        <a href="#" class="boldd color-green"  data-toggle="modal"
                        data-target="#kt_modal_2"> Lihat Gambar</a>
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
                    <label class="boldd mb1">List Palet</label>
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
                        {{-- <div class="card">
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
                        </div> --}}
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
                                    <a class="fancybox" rel="ligthbox"
                                        href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}">
                                        <img class="img-fluid"
                                            src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}" alt=""
                                            srcset="">
                                    </a>
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

<script src="{{asset('assets/extends/plugin/fancybox-simple/jquery.fancybox.min.js')}}"></script>
<script type="text/javascript">
    const id_gudang = "{{$id_gudang}}";
    const id_aktivitas_harian = "{{$id_aktivitas_harian}}";

    let ajaxUrl = baseUrl + "log-aktivitas",
    ajaxSource = ajaxUrl;

    $(".fancybox").fancybox({
        openEffect: "none",

        closeEffect: "none"
    });

    function loadArea(id_material) {
        $.ajax({
            url:ajaxSource+'/get-area/'+id_gudang+"/"+id_material+"/"+id_aktivitas_harian,
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
                    // console.log(element);
                    // element.forEach(element2 => {
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
                    // });
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
                // console.log($("#accordionExample5"));
                $("#tempat_card").html(temp);
                // console.log(temp);
            },
            error:(response) => {

            }
        });
    }

</script>

@stop