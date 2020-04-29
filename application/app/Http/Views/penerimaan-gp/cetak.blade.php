<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
                           
                                <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                            
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
                <h2 class="ml1">Serah Terima Pemuatan Pupuk</h2>
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
                        {{$aktivitasHarian->aktivitas->nama}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Shift Kerja
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->shift->nama??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Gudang Tujuan
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->gudangTujuan->nama??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Paket Alat Berat
                    </label>
                    <p class="boldd-500">
                        @php $no = 1; @endphp
                        @foreach ($aktivitasHarian->aktivitasHarianAlatBerat as $key)
                        {{ $no.'. '.$key->nomor_lambung}} <br>
                        @php $no++ @endphp
                        @endforeach
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Sistro
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->sistro??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Nopol
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->nopol??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Driver
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->driver??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        No. SO / Posto
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->posto??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        Nama Checker
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->checker->name??'-'}}
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
                            <thead>
                            <tr>
                              <th scope="col">Area</th>
                              <th scope="col">Tanggal</th>
                              <th scope="col">Daya Tampung</th>
                            </tr>
                            </thead>
                            @foreach ($produk as $item)
                                <tr>
                                    <td>Area {{ $item->nama_area }}</td>
                                    <td>{{ helpDate($item->tanggal, 'mi') }}</td>
                                    <td>{{ $item->jumlah }} Ton</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="col-md-5 mb1 ml1">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">Jenis dan Jumlah</th>
                                    <th scope="col">Tipe Aktivitas</th>
                                  </tr>
                            </thead>
                            @foreach ($pallet as $key)
                                @if ($key->status_pallet == 1)
                                    @php $status = 'Stok' @endphp
                                @elseif ($key->status_pallet == 2)
                                    @php $status = 'Terpakai' @endphp
                                @elseif ($key->status_pallet == 3)
                                    @php $status = 'Kosong' @endphp
                                @else
                                    @php $status = 'Rusak' @endphp
                                @endif
                                <tr>
                                <td>{{$key->material->nama}} - {{$key->jumlah}} ( Pallet {{ $status }} )</td>
                                <td>{{ $key->tipe == 1?'Mengurangi':'Menambah' }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="col-md-12 mb1">
                        <strong>
                            <h5>Dengan ini menyatakan bahwa : </h5>
                            <h5>1. Produk yang diterima supir adalah dalam kondisi Original / Siap Jual</h5>
                            <h5>2. Jumlah produk yang diterima sesuai kuantum tertulis</h5>
                            <h5>3. Penataan produk dalam truk sudah sesuai standar (tertata dengan rapi) serta sesuai dengan foto yang terlampir</h5>
                        </strong>
                    </div>
                    <div class="col-md-12 mb1">
                        <h5>Dengan mengetahui kondisi dalam 3 poin diatas, dengan ini saya menyatakan secara sadar bahwa produk diterima dengan baik dan sesuai persyaratan, sehingga apabila ditemukan kondisi tampilan, penataan dan jumlah produk atas pengiriman ini yang tidak sesuai dengan ketentuan adalah bukan dari tanggung jawab PT Petrokimia Gresik (Dep. Distribusi Wilayah I, Bagian Gudang Gresik)</h5>
                    </div>
                </section>
            </div>
        </div>
        <div class="footer">
            <div class="row col-md-12" style="padding: 0;float: right; margin: 0 2cm 2rem 0;transform: translateY(-4rem); display: block; text-align:center">
                <p>Tanda Tangan</p>
                @if (file_exists(storage_path("/app/public/aktivitas_harian/" . $aktivitasHarian->id . "/" . $aktivitasHarian->ttd)))
                    <img class="imagIttd p-setengah mb1"
                        src="{{url('watch').'/'.$aktivitasHarian->ttd.'?un='.$aktivitasHarian->id.'&ctg=aktivitas_harian&src='.$aktivitasHarian->ttd}}" alt=""
                        srcset="">
                @else
                    <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                @endif
                <p>({{$aktivitasHarian->driver??'...........................'}})</p>
            </div>
        </div>
    </div>
    <div class="page" style="padding: 1cm">
    <section class="row col-md-12 mt2" style="margin:0">
        <label class="boldd-500 col-md-12 mb1">
            Foto Truk
        </label>
            @foreach ($aktivitasFoto as $item)
            <div class="col-md-4" style="margin: .5rem 0;">
                @if ($aktivitasFoto->isEmpty()) 
                    <h4>Tidak ada foto</h4>
                @endif
                @if ($item->fotoJenis)
                    <h6 style="font-weight: normal !important;"> {{$item->fotoJenis->nama}} </h6>
                    @if (file_exists(storage_path("/app/public/aktivitas_harian/" . $item->id_aktivitas_harian . "/" . $item->foto)))
                        <a class="fancybox" rel="ligthbox"
                            href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}">
                            <img class="img-fluid"
                                src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=aktivitas_harian&src='.$item->foto}}" alt=""
                                srcset="">
                        </a>
                    @else
                        <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                    @endif
                @endif
            </div>
            @endforeach
    </section>
    <section class="row col-md-12 mt2" style="margin:0">
        <label class="boldd-500 col-md-12 mb1">
            Foto Kelayakan
        </label>
        <div class="col-md-4">
            <h6 style="font-weight: normal !important;"> Sebelum </h6>
            @if ($fotoKelayakanBefore->isEmpty())
                <h4>Tidak ada foto</h4>
            @endif
            @foreach ($fotoKelayakanBefore as $item)
                @if (file_exists(storage_path("/app/public/kelayakan/" . $item->id_aktivitas_harian . "/" . $item->file_enc)))
                <a class="fancybox" rel="ligthbox"
                    href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}">
                    <img class="img-fluid"
                        src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}" alt=""
                        srcset="">
                </a>
                @else
                    <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                @endif
            @endforeach
        </div>
    </section>
    <section class="row col-md-12 mt1" style="margin:0">
        <div class="col-md-4">
            <h6 style="font-weight: normal !important;"> Sesudah </h6>
                @if ($fotoKelayakanAfter->isEmpty())
                <h4>Tidak ada foto<h4>
            @endif
                @foreach ($fotoKelayakanAfter as $item)
                @if (file_exists(storage_path("/app/public/kelayakan/" . $item->id_aktivitas_harian . "/" . $item->file_enc)))
                <a class="fancybox" rel="ligthbox"
                    href="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}">
                    <img class="img-fluid"
                        src="{{url('watch').'/'.$item->foto.'?un='.$item->id_aktivitas_harian.'&ctg=kelayakan&src='.$item->file_enc}}" alt=""
                        srcset="">
                </a>
                @else
                    <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                @endif
            @endforeach
        </div>
    </section>
    </div>
</div>

<a href="javascript:;" class="float no-print" onclick="printPrev()">
    <i class="fa fa-print my-float" style="font-size: 30px;"></i>
</a>

<script>
    function printPrev(){
        window.print();
    }
</script>


{{-- <script src="{{asset('assets/extends/js/page/cetak-penerimaan-gp.js')}}"></script> --}}
{{-- 
@stop --}}