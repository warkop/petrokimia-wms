<link rel="stylesheet" href="{{aset_extends('css/global.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
<link rel="stylesheet" href="{{asset('assets/extends/css/print/css/mainpage.css')}}">
<link rel="shortcut icon" href="{{asset('assets/extends/img/logo/favwms.png')}}">
<style>
    .v-middle-flex-center {
        display: flex;
        align-items: center;
    }
</style>

{{-- <style>
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
</style> --}}

<!-- begin:: Content -->
{{-- <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
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
                            <h5 class="boldd"> </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Shift Kerja</label>
                            <h5 class="boldd"> </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Gudang Tujuan</label>
                            <h5 class="boldd"> </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Paket Alat Berat</label>
                            
                            <h5 class="boldd"> 
                                
                            </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Sistro</label>
                            <h5 class="boldd"> </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Nopol</label>
                            <h5 class="boldd"></h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>Driver</label>
                            <h5 class="boldd"> </h5>
                        </div>
                    </div>
                    <div class="row mb1">
                        <div class="col-12">
                            <label>No. SO</label>
                            <h5 class="boldd"> </h5>
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
                            
                            <div class="kt-widget4__item border-bottom-dash">
                                <div class="kt-widget4__info">
                                    <p class="kt-widget4__username">
                                         - <span class="boldd"> Ton</span>
                                    </p>
                                     
                                        <p class="kt-widget4__text color-oren boldd">
                                            Mengurangi    
                                        </p>
                                    
                                        <p class="kt-widget4__text color-green boldd">
                                            Menambah
                                        </p>
                                    
                                </div>
                                <a href="#" class="btn btn-sm btn-brand btn-bold" data-toggle="modal"
                                data-target="#kt_modal" >Area</a>
                            </div>
                            
                            <div class="border-pembatas mb1"></div>
                        </div>
                    </div>
                    <div class="row listterplas mt2">
                        <label class="boldd mb1">List Pallet</label>
                        <div class="kt-widget4 col-12 kel">
                            
                                <div class="kt-widget4__item border-bottom-dash">
                                    <div class="kt-widget4__info">
                                        <p class="kt-widget4__username">
                                            
                                        </span>
                                        </p>
                                        
                                            <p class="kt-widget4__text color-oren boldd">
                                                Mengurangi    
                                            </p>
                                        
                                            <p class="kt-widget4__text color-green boldd">
                                                Menambah
                                            </p>
                                        
                                    </div>
                                </div>    
                            
                            <div class="border-pembatas mb1"></div>
                        </div>
                        <div class="row mb1">
                        <div class="col-12">
                            <label>Tanda Tangan</label><br>
                            
                                <a class="fancybox" rel="ligthbox">
                                    
                                </a>
                           
                                <span class="kt-link kt-link--brand kt-font-bolder"><strong>File Tidak ada di server</strong></span>
                            
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
                        <a href="javascript:;" type="button" class="btn btn-success btn-lg" onclick="printPrev()" > <i class="fa fa-print"></i> Print Preview</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="book">
    <div class="page">
        <div class="header">
            <section class="v-middle-flex-center ml1">
                <img alt="Logo" src="{{aset_extends()}}/img/logo/logo_wms1.png" width="20%"/>
                <h2 class="ml1">Report Warehouse Mangement System</h2>
            </section>
        </div>
        <div class="isi" style="padding: .5cm 1.5cm !important;">
            {{-- <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class="uppercase underline" style="font-size: 16pt">Surat - keterangan</h3>
                    <p style="margin-top: -1rem">Nomor : 0020/NK.01.01/04/KR/2018</p>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-6 mb1">
                    <label>
                        Jenis Aktivitas
                    </label>
                    <p class="boldd-500">
                        Pengiriman Gudang Penyangga Non Urea
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Shift Kerja
                    </label>
                    <p class="boldd-500">
                        Shift 2
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Gudang Tujuan
                    </label>
                    <p class="boldd-500">
                        KEDIRI 3 GURAH
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Paket Alat Berat
                    </label>
                    <p class="boldd-500">
                        1. 162
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Sistro
                    </label>
                    <p class="boldd-500">
                        SISTRO_GCS_TyMvz4nHV_sec1
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Nopol
                    </label>
                    <p class="boldd-500">
                        W 9051 UH
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Driver
                    </label>
                    <p class="boldd-500">
                        YUANGGA
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        No. SO
                    </label>
                    <p class="boldd-500">
                        5120180888
                    </p>
                </div>
                <section class="row col-md-12 mt2" style="margin:0">
                    <div class="col-md-6 mb-setengah">
                        <label class="boldd-500">
                            List Produk
                        </label>
                    </div>
                    <div class="col-md-6 mb-setengah">
                        <label class="boldd-500 ml1">
                            List Palet
                        </label>
                    </div>

                    <div class="col-md-6 mb1">
                        <table>
                            <tr>
                              <th>Area</th>
                              <th>Tanggal</th>
                              <th>Daya Tampung</th>
                            </tr>
                            <?php for ($i=0; $i < 3; $i++) { ?>
                            <tr>
                              <td>Area B1</td>
                              <td>04 Februari 2020</td>
                              <td>30 Ton</td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>

                    <div class="col-md-5 mb1 ml1">
                        <table>
                            <?php for ($i=0; $i < 2; $i++) { ?>
                            <tr>
                              <td>Pallet Pupuk Plastik - 30 ( Pallet Terpakai )</td>
                              <td>Mengurangi</td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </section>
                <section class="row col-md-12 mt2" style="margin:0">
                    <label class="boldd-500 col-md-12 mb1">
                        Foto Truk
                    </label>
                    <div class="col-md-3">
                        <h6> Tampak Samping Kiri </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794298471.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794298471.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-3">
                        <h6> Tampak Belakang </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794303037.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794303037.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-3">
                        <h6> Tampak Atas </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794311676.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794311676.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-3">
                        <h6> Tampak Samping Kanan </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794328216.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794328216.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-3">
                        <h6> Tampak Bawah </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794298471.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794298471.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-3">
                        <h6> Tampak Depan </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794298471.jpg?un=3210&ctg=aktivitas_harian&src=ei_1580794298471.jpg" alt="" srcset="">
                    </div>
                </section>
                <section class="row col-md-12 mt2 foto-kelayakan" style="margin:0">
                    <label class="boldd-500 col-md-12 mb1">
                        Foto Kelayakan
                    </label>
                    <div class="col-md-6">
                        <h6> Sebelum </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/1580794250106.jpg?un=3210&ctg=kelayakan&src=483ba6181eea1ff47d0261dd354b780d.jpg" alt="" srcset="">
                    </div>
                    <div class="col-md-6">
                        <h6> Sesudah </h6>
                        <img class="imagImage p-setengah mb1 " src="http://demo.energeek.co.id/petrokimia-wms-dev/watch/ei_1580794280038.jpg?un=3210&ctg=kelayakan&src=a7e4a5e5defccbb7a9a1181b24e78ef1.jpg" alt="" srcset="">
                    </div>
                </section>
            </div>
        </div>
        <div class="footer mt1">
            <div class="row col-md-12" style="padding: 0;float: right; margin: 0 1cm 2rem 0;transform: translateY(-2rem); display: block; text-align:center">
                <p>Tanda Tangan</p>
                <img class="imagIttd p-setengah mb1 " src="http://devwms.petrokimia-gresik.com/watch/1b57a198-6fa0-4022-9cd4-6e5d2fb9e958.jpg?un=7845&ctg=aktivitas_harian&src=1b57a198-6fa0-4022-9cd4-6e5d2fb9e958.jpg" alt="" srcset="">
            </div>
        </div>
    </div>
</div>

<script>
    function printPrev(){
        window.print();
    }
</script>


{{-- <script src="{{asset('assets/extends/js/page/cetak-penerimaan-gp.js')}}"></script> --}}
{{-- 
@stop --}}