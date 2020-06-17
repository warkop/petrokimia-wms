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
<title>Cetak Aktivitas</title>
<div class="book">
    <div class="page">
        <div class="header">
            <section class="v-middle-flex-center ml1">
                <img alt="Logo" src="{{aset_extends()}}/img/logo/logo_wms1.png" width="20%"/>
                <h2 class="ml1">Data Cancelation WMS</h2>
            </section>
        </div>
        <div class="isi" style="padding: .5cm 1.5cm !important;">
            <div class="row">
                <div class="col-md-6 mb1">
                    <label>
                        JENIS AKTIVITAS YANG DICANCEL
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->aktivitas->nama??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        SHIFT KERJA
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->shift->nama??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        SISTRO/ NO SO
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->sistro??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        NOPOL
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->nopol??'-'}}
                    </p>
                </div>
                <div class="col-md-6 mb1">
                    <label>
                        CHECKER
                    </label>
                    <p class="boldd-500">
                        {{$aktivitasHarian->checker->name??'-'}}
                    </p>
                </div>
                <section class="row col-md-12 mt2" style="margin:0">
                    <div class="col-md-6 mb-setengah">
                        <label class="boldd-500">
                            DETAIL PRODUK
                        </label>
                    </div>
                    <div class="col-md-6 mb-setengah">
                        <label class="boldd-500 ml1">
                            DETAIL PALLET
                        </label>
                    </div>

                    <div class="col-md-6 mb1">
                        @if (count($produk) > 0)
                        <table>
                            <thead>
                            <tr>
                              <th scope="col">PRODUK</th>
                              <th scope="col">TANGGAL PRODUK</th>
                              <th scope="col">QUANTUM</th>
                            </tr>
                            </thead>
                            @foreach ($produk as $item)
                                <tr>
                                    <td>Area {{ $item->nama_material }}</td>
                                    <td>{{ helpDate($item->tanggal, 'mi') }}</td>
                                    <td>{{ $item->jumlah }} Ton</td>
                                </tr>
                            @endforeach
                        </table>
                        @else
                            <p><strong>Tidak ada produk dalam transaksi</strong></p>
                        @endif
                    </div>

                    <div class="col-md-5 mb1 ml1">
                        @if (count($pallet) > 0)
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
                        @else
                            <p><strong>Tidak ada pallet dalam transaksi</strong></p>
                        @endif
                    </div>
                </section>
                {{-- <div class="col-md-12 mb1">
                    <label>
                        DOKUMENTASI BERITA ACARA
                    </label>
                    <div class="text-center">
                        <img src="{{asset('assets/main/metronic/media/blog/surat.jpg')}}" width="60%"/>
                    </div>
                </div> --}}
            </div>
        </div>
        {{-- <div class="footer">
            <div class="row col-md-12" style="padding: 0;float: right; margin: 0 2cm 2rem 0;transform: translateY(-4rem); display: block; text-align:center">
                <p>Tanda Tangan</p>
                    <span class="kt-link kt-link--brand kt-font-bolder _404fileImg"><strong>File Tidak ada di server</strong></span>
                <p>...........................</p>
            </div>
        </div> --}}
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