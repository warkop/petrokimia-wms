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
                <a href="{{url('penerimaan-gp')}}" class="pointer"><span class="pull-right color-dodolo"><i class="la la-arrow-left"></i>kembali</span></a>
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
                        <h5 class="boldd"> Paket A</h5>
                    </div>
                </div>
                <div class="row mb1">
                    <div class="col-12">
                        <label>Sistro</label>
                        <h5 class="boldd"> {{$aktivitasHarian->sistro}}</h5>
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
                        <div class="kt-widget4__item border-bottom-dash">
                            <div class="kt-widget4__info">
                                <p class="kt-widget4__username">
                                    Pupuk Urea - <span class="boldd">100 Ton</span>
                                </p>
                                <p class="kt-widget4__text color-oren boldd">
                                    Mengurangi
                                </p>
                            </div>
                            <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                data-target="#kt_modal">Area</a>
                        </div>
                        <div class="kt-widget4__item border-bottom-dash ">
                            <div class="kt-widget4__info">
                                <p class="kt-widget4__username">
                                    Pupuk ZE - <span class="boldd">20 Ton</span>
                                </p>
                                <p class="kt-widget4__text color-oren boldd">
                                    Mengurangi
                                </p>
                            </div>
                            <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                data-target="#kt_modal">Area</a>
                        </div>
                        <div class="border-pembatas mb1"></div>
                    </div>
                </div>
                <div class="row listterplas mt2">
                    <label class="boldd mb1">List Palet</label>
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
                <h5 class="modal-title" id="exampleModalLabel">Foto Log Aktivitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="kt-scroll" data-scroll="true" data-height="400">
                        <div class="row mb2">
                            <div class="col-4">
                                <label class="boldd">Foto Atas</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product1.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product1.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Bawah</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product3.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product3.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Depan</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product3.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product3.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
                        </div>
                        <div class="row mb2">
                            <div class="col-4">
                                <label class="boldd">Foto Belakang</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product4.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product4.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kanan</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product5.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product5.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
                            <div class="col-4">
                                <label class="boldd">Foto Kiri</label>
                                <a class="fancybox" rel="ligthbox" href="{{asset('assets/metronic/assets/media/products/product6.jpg')}}">
                                    <img class="img-fluid" src="{{asset('assets/metronic/assets/media/products/product6.jpg')}}" alt="" srcset="">
                                </a>
                            </div>
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

  $(".fancybox").fancybox({

      openEffect: "none",

      closeEffect: "none"

  });

</script>

@stop