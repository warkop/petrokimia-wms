<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
<script>
    document.title = "Dashboard | Warehouse Management System";
    WebFont.load({
        google: {
            "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
        },
        active: function () {
            sessionStorage.fonts = true;
        }
    });
</script>
@include('layout.header')
<link rel="stylesheet" href="{{aset_extends('css/global.css')}}">
<script type="text/javascript" src="{{aset_extends('plugin/gchart/loader.js')}}"></script>
<!-- <script type="text/javascript" src="{{aset_tema('app/custom/general/crud/forms/widgets/bootstrap-daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{aset_tema('app/custom/general/crud/forms/widgets/bootstrap-daterangepicker.min.js')}}"></script> -->
<!-- <script type="text/javascript" src="{{aset_tema('app/custom/general/crud/forms/widgets/select2.js')}}"></script>
<script type="text/javascript" src="{{aset_tema('app/custom/general/crud/forms/widgets/select2.min.js')}}"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> -->


<style>
.mbox {   
    display: inline-block;
    width: 10px;
    height: 10px;
    margin: 10px 55px 10px 25px;
    padding-left: 4px;
}

.gudang-info td{
    padding:2px;
}

.gudang-info{
    margin-left:auto; 
    margin-right:auto;
    font-size:14px;
}
</style>

<div class="row row-no-padding row-col-separator-xl" style="background:#fff">
    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-6 pointer nav---gation" onclick="location.href='{{url('/')}}';">
        <div class="kt-widget24">
            <div class="text-center">
                <div class="text-center">
                    <a href="{{url('/')}}">
                        <h4> <span><i class=""></i></span> Halaman Depan</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-6 pointer nav---gation" onclick="location.href='{{url('layout')}}';" style="z-index:10">
        <div class="kt-widget24">
            <div class="text-center">
                <div class="text-center">
                    <a href="{{url('layout')}}">
                        <h4><span><i class=""></i></span>Menu Utama</h4>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- <div class="kt-content  kt-grid__item kt-grid__item--fluid" style="margin-bottom: -3vh">
    <div class="row">
        <div class="col-12">
            <div class="kt-portlet">
                <div class="kt-portlet__body">
                    <div class="row">
                        <label class="col-form-label">Periode 1</label>
                        <div class="col-2">
                            <input class="form-control" id="kt_datepicker_1" readonly placeholder="Pilih periode mulai"
                                type="text" />
                        </div>
                        <label class="offset-1 col-form-label">Periode 2</label>
                        <div class="col-2">
                            <input class="form-control" id="kt_datepicker_2" readonly
                                placeholder="Pilih periode selesai" type="text" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


<div class="kt-content  kt-grid__item kt-grid__item--fluid">
    <!--Begin::Dashboard 6-->
    <div id="row-chartLine" class="row v-middle-flex-center">
        {{-- <section> --}}
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="kt-portlet">
                    <div class="kt-portlet__head no-border-bottom">
                        <div class="kt-portlet__head-title">
                            <h4 class="kt-portlet__head-text title_sub pt-4">
                                <br>
                                Dashboard WMS
                                </h4>
                                <!-- <p class="sub">
                                    Berikut ini adalah statistk pengiriman barang per-shift pada <span
                                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                                </p> -->
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group pt-4">
                                {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class=" row" style="margin-bottom:3rem">
                            <!-- <label class="col-form-label col-2 font-weight-bold">Periode 1</label>
                            <div class="col-3">
                                <input class="form-control" id="kt_datepicker_1" readonly placeholder="Pilih periode mulai"
                                    type="text" />
                            </div>
                            <label class="offset-1 col-form-label col-2 font-weight-bold">Periode 2</label>
                            <div class="col-3">
                                <input class="form-control" id="kt_datepicker_2" readonly
                                    placeholder="Pilih periode selesai" type="text" />
                            </div> -->
                            <div class="form-group col-md-3">
                                
                            
                            <input type="text" class="form-control" id="kt_daterangepicker_2" readonly placeholder="Pilih Periode" type="text" />
                            
                            </div>
                            <div class="form-group col-md-3">
                                
                            <select class="form-control input-enter m-select2" id="pilih_shift" name="param" >
                                <option disabled selected>Pilih shift</option>
                                <option>Shift 1</option>
                                <option>Shift 2</option>
                                <option>Shift 3</option>

                            </select>
                            </div>
                            <div class="form-group col-md-3">
                                
                            <select class="form-control input-enter m-select2" id="pilih_gudang" name="param" >
                                <option disabled selected>Pilih gudang</option>
                                <option>Gudang PF 2</option>
                                <option>Gudang Multi Guna</option>
                                <option>Gudang Curah 50.000</option>
                            </select>
                            </div>
                            <div class="form-group col-md-2">
                                
                            <button type="button" class="btn btn-primary" style="width:100%">Filter</button>
                            </div>
                            <div class="form-group col-md-1">
                                
                            <button type="button" class="btn btn-danger btn-icon"><i class="la la-refresh"></i></button>&nbsp;
                            </div>
                        </div>

                        <div class="row">
                            <!-- <p> 
                                <span class="mr1"> <i class="fa fa-square gd-a-color-1"></i> Gudang Ponska</span>
                                <span class="mr1"> <i class="fa fa-square gd-a-color-2"></i> Gudang Amurea</span>
                                <span class="mr1"> <i class="fa fa-square gd-a-color-3"></i> Gudang Petrocas</span>
                            </p> -->
                            <div class="col-md-4">
                            <h5>Realisasi Handling Per Jenis Produk Gudang Gresik</h5>
                            <div id="jenisproduk" style="width:100%; height:350px;"></div>
                            </div>
                            <div class="col-md-4">
                            <h5>Realisasi Handling Per Gudang</h5><br/>
                            <div id="gudang" style="width:100%; height:350px;"></div>
                            </div>
                            <div class="col-md-4">
                            <h5>Realisasi Tanase Produk Rusak</h5><br/>
                            <div id="produkrusak" style="width:100%; height:350px;"></div>
                            </div>
                        </div>
                        <div class="row mt4">
                            
                            <div class="col-md-6">
                            <h5>Produksi VS Pengeluaran</h5>
                            <div id="produksipengeluaran" style="height: 500px;"></div>
                            </div>
                            <div class="col-md-6">
                            <h5>Kapasitas Muat Buruh VS Realisasi Muat</h5>
                            <div id="muatan" style="height: 500px;"></div>
                            </div>
                           
                        </div>
                        <div class="mt4">
                        <h5>Managemen Layout Produk</h5>
                        <div class="row mt2">
                        <div class="col-md-8 pr-0">
                        <div id="map" style="width:100%; height:500px;"></div>
                        </div>
                        <div class="col-md-4 pl-0" style="background-color:#FFC201">
                            <div >
                                <div style="text-align: center; margin-top:50%">
                                    <img src="{{aset_extends('img/logo/map-1.png')}}"/><br/>
                                    <h2>Gudang Phonska</h2>
                                    <table class="gudang-info" style="border:none;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Kapasitas</th>
                                            <th scope="col"></th>
                                            <th scope="col">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>Kapasitas Indoor</td>
                                        <td>:</td>
                                        <td>15.000 Ton</td>
                                    </tr>
                                    <tr>
                                        <td>Stok Indoor</td>
                                        <td>:</td>
                                        <td>7.000 Ton</td>
                                    </tr>
                                    <tr>
                                        <td>Sisa Kapasitas Indoor</td>
                                        <td>:</td>
                                        <td>8.000 Ton</td>
                                    </tr>
                                    <tr>
                                        <td>Kapasitas Outdoor</td>
                                        <td>:</td>
                                        <td>30.000 Ton</td>
                                    </tr>
                                    <tr>
                                        <td>Stok Outdoor</td>
                                        <td>:</td>
                                        <td>28.000 Ton</td>
                                    </tr>
                                    <tr>
                                        <td>Sisa Kapasitas Outdoor</td>
                                        <td>:</td>
                                        <td>2.000 Ton</td>
                                    </tr>
                                    </table>
                                </div >
                                    
                            </div>
                        

                       
                        </div>
                        </div>
                        </div>
                        <div class="row mt3 ">
                            
                            <div class="col-md-8">
                            <h5>Diagram Realisasi Penggunaan Alat Berat Gudang Gresik</h5>
                            <div id="realisasialatberat" style="height: 400px;"></div>
                            </div>
                            <div class="col-md-4">
                            <h5>Laporan Keluhan Alat Berat</h5>
                            <div id="keluhanmuatan" style="height: 400px;"></div>
                            </div>
                           
                        </div>
                        <div>
                        <h5 class="mt4">Stok Palet dan Terplas Per Tanggal 1 Febuary 2020</h5>
                        <div id="stokpaletbulan" style="height: 500px;"></div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 mb2">
                                <canvas id="line-chart" width="800" height="300"></canvas>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 mb2">
                                <div class="row">
                                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                                        <div class="kt-portlet">
                                            <div class="kt-portlet__head no-border-bottom">
                                                <div class="kt-portlet__head-title">
                                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                                        Shift 1
                                                    </h5>
                                                </div>
                                                <div class="kt-portlet__head-toolbar">
                                                    <div class="kt-portlet__head-group pt-4">
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kt-portlet__body" style="padding: 1rem 2rem;">
                                                <h2 class="contains-chart-result" style="color:#00AE4D">600 Ton</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                                        <div class="kt-portlet">
                                            <div class="kt-portlet__head no-border-bottom">
                                                <div class="kt-portlet__head-title">
                                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                                        Shift 2
                                                    </h5>
                                                </div>
                                                <div class="kt-portlet__head-toolbar">
                                                    <div class="kt-portlet__head-group pt-4">
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kt-portlet__body" style="padding: 1rem 2rem;">
                                                <h2 class="contains-chart-result" style="color:#FAAE32">700 Ton</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                                        <div class="kt-portlet">
                                            <div class="kt-portlet__head no-border-bottom">
                                                <div class="kt-portlet__head-title">
                                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                                        Shift 3
                                                    </h5>
                                                </div>
                                                <div class="kt-portlet__head-toolbar">
                                                    <div class="kt-portlet__head-group pt-4">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kt-portlet__body" style="padding: 1rem 2rem;">
                                                <h2 class="contains-chart-result" style="color:#E14A3A">400 Ton</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 mb2">
                <div class="row">
                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head no-border-bottom">
                                <div class="kt-portlet__head-title">
                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                        Shift 1
                                    </h5>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-group pt-4">
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <h2 class="contains-chart-result" style="color:#00AE4D">600 Ton</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head no-border-bottom">
                                <div class="kt-portlet__head-title">
                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                        Shift 2
                                    </h5>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-group pt-4">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <h2 class="contains-chart-result" style="color:#FAAE32">700 Ton</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-4">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head no-border-bottom">
                                <div class="kt-portlet__head-title">
                                    <h5 class="kt-portlet__head-text title_sub pt-4">
                                        Shift 3
                                    </h5>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-group pt-4">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <h2 class="contains-chart-result" style="color:#E14A3A">400 Ton</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        {{-- </section> --}}
    </div>
    <!-- <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-6">
            <div class="kt-portlet">
                <div class="kt-portlet__head no-border-bottom">
                    <div class="kt-portlet__head-title">
                        <h5 class="kt-portlet__head-text title_sub pt-4">
                            <br>
                            Grafik Perbandingan Stock dan Kapasitas Gudang
                            </h4>
                            <p class="sub">
                                Statistk perbandingan stock dan kapasitas gudang pada <span
                                    class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                            </p>
                    </div>
                </div>
                <div class="kt-portlet__body"w>
                    <canvas id="bar-chart-perbadingan"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-6">
            <div class="kt-portlet">
                <div class="kt-portlet__head no-border-bottom">
                    <div class="kt-portlet__head-title">
                        <h5 class="kt-portlet__head-text title_sub pt-4">
                            <br>
                            Tabel Kondisi Palet
                            </h4>
                            <p class="sub">
                                Berikut ini adalah tabel rangkuman kondisi palet pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                            </p>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group pt-4">
                            {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body" style="min-height:400px; max-height:400px;">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="dttb-kondisi-palet" >
                        <thead>
                            <tr>
                                <th style="font-size:14px">Nama Gudang</th>
                                <th style="font-size:14px" class="text-center">Ketersediaan Palet</th>
                                <th style="font-size:14px" class="text-center">Kemampuan Tampung (Hari)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> -->

</div>

{{-- <div class=" kt-content  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet">
                    <div class="kt-portlet__head no-border-bottom">
                        <div class="kt-portlet__head-title">
                            <h5 class="kt-portlet__head-text title_sub pt-4">Keluhan Alat berat</h5>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row">
                            <canvas id="bar-chart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet">
                    <div class="kt-portlet__head no-border-bottom">
                        <div class="kt-portlet__head-title">
                            <h5 class="kt-portlet__head-text title_sub pt-4">Komplain GP</h5>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="">
                            <canvas id="line-chart-lancip" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


@include('layout.footer')
<script src="{{('assets/extends/js/page/dashboard.js')}}" defer></script>

<script>
$(function() {
    dataTableKondisiPalet();
    toggle();
});

    function toggle() {
        if (window.innerWidth < 800) {
            $('#row-chartLine').removeClass('v-middle-flex-center'); 
        }
        else {
            $('#row-chartLine').addClass('v-middle-flex-center');         
        }    
    }

    // line-Chart lengkung
    new Chart(document.getElementById("line-chart"), {
        type: 'line',
        data: {
            labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15"],
            datasets: [{
                data: [86, 100, 106, 20, 107, 24, 133, 100, 90, 100, 120, 200, 180, 90, 250],
                label: "Gudang 1, Alat Berat: Rp 1.000.000, SDM: Rp 800.000",
                // label: "Shift 6",
                borderColor: "#00AE4D",
                fill: false
            }, {
                data: [55, 77, 66, 88, 99, 118, 177, 144, 44, 22, 333, 11, 10, 12, 100],
                label: "Gudang 2, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#B51C64",
                fill: false
            }, {
                data: [96, 90, 196, 290, 307, 234, 233, 300, 190, 200, 320, 500, 780, 290, 250],
                label: "Gudang 3, Alat Berat: Rp 3.000.000, SDM: Rp 1.800.000",
                borderColor: "#8653B5",
                fill: false
            }, {
                data: [86, 100, 106, 20, 107, 24, 133, 100, 90, 100, 120, 200, 180, 90, 250],
                label: "Gudang 4, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#FAAE32",
                fill: false
            }, {
                data: [6, 140, 106, 200, 100, 3, 554, 87, 32, 223, 322, 123, 555, 13, 12],
                label: "Gudang 5, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#B56D1C",
                fill: false
            }, {
                data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 19, 11, 12, 13, 14, 15],
                label: "Gudang 6, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#79DB22",
                fill: false
            }, {
                data: [2, 10, 20, 30, 40, 50, 60, 70, 80, 90, 120, 100, 110, 130, 150],
                label: "Gudang 7, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#3873B5",
                fill: false
            }, {
                data: [4, 33, 66, 88, 99, 10, 133, 177, 200, 21, 33, 66, 145, 120, 100],
                label: "Gudang 8, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#27A5D7",
                fill: false
            }, {
                data: [66, 77, 88, 99, 100, 111, 122, 133, 333, 122, 155, 177, 222, 111, 10],
                label: "Gudang 9, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#DE232F",
                fill: false
            }, {
                data: [56, 10, 06, 50, 17, 24, 33, 10, 9, 10, 12, 20, 18, 9, 250],
                label: "Gudang 10, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#FAAE32",
                fill: false
            },
        ]
        },
        options: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: 'Ton per Shift'
            }
        }
    });

    //Bar Chart 
    var barChartData = {
  labels: [
    "Gd. Ponska",
    "Gd. Amurea",
    "Gd. Petrocas"
  ],
  datasets: [
    {
      label: "Kapasitas Gudang",
      backgroundColor: "#E23D6C",
      borderColor: "#E23D6C",
      borderWidth: 1,
      data: [90,70,60]
    },
    {
      label: "Stok Pupuk saat ini",
      backgroundColor: "#2171A0",
      borderColor: "#2171A0",
      borderWidth: 1,
      data: [100,70,70]
    }
    ]
    };

    var chartOptions = {
    responsive: true,
    legend: {
        position: "top"
    },
    title: {
        display: true,
        // text: "Chart.js Bar Chart"
    },
    scales: {
        yAxes: [{
        ticks: {
            beginAtZero: true
        }
        }]
    }
    }

    window.onload = function() {
    var ctx = document.getElementById("bar-chart-perbadingan").getContext("2d");
    window.myBar = new Chart(ctx, {
        type: "bar",
        data: barChartData,
        options: chartOptions
    });
    };

    //END Bar Chart 

    //Dttb Kondisi Palet


	function dataTableKondisiPalet() {
		var table = $('#dttb-kondisi-palet');

        var dataJSONArray = JSON.parse(
        '[["Gd. Ponska", "200", "7 hari"], ["Gd. Amurea", "100", "10 hari"], ["Gd. Petrocas", "500", "19 hari"]]');

		table.DataTable({
			responsive: true,
            data: dataJSONArray,
			pagingType: 'full_numbers',
			columnDefs: [
			],
		});
	};



    let dataShift1 = [];
    @if (!empty($shift1))
        @foreach ($shift1 as $item)
            dataShift1.push({{$item->shift}});
        @endforeach
    @endif

    let dataShift2 = [];
    
    @if (!empty($shift2))
        @foreach ($shift2 as $item)
            dataShift2.push({{$item->shift}});
        @endforeach
    @endif
    
    let dataShift3 = [];
    @if (!empty($shift3))
        @foreach ($shift3 as $item)
            dataShift3.push({{$item->shift}});
        @endforeach    
    @endif
    // chart bar 
    new Chart(document.getElementById("bar-chart"), {
        type: 'bar',
        data: {
            labels: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
            datasets: [{
                data: dataShift1,
                backgroundColor: "#00AE4D",
                label: "Shift 1",
                fill: false
            }, {
                data: dataShift2,
                backgroundColor: "#FAAE32",
                label: "Shift 2",
                fill: false
            }, {
                data: dataShift3,
                backgroundColor: "#E14A3A",
                label: "Shift 3",
                fill: false
            }]
        },
        options: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: 'Keluhan Alat berat pershift'
            }
        }
    });

    let keluhanGpShift1 = [];
    @if (!empty($komplain_gp_shift1))
        @foreach ($komplain_gp_shift1 as $item)
            keluhanGpShift1.push({{$item->shift}});
        @endforeach
    @endif

    let keluhanGpShift2 = [];
    @if (!empty($komplain_gp_shift2))
        @foreach ($komplain_gp_shift2 as $item)
            keluhanGpShift2.push({{$item->shift}});
        @endforeach
    @endif

    let keluhanGpShift3 = [];
    @if (!empty($komplain_gp_shift3))
        @foreach ($komplain_gp_shift3 as $item)
            keluhanGpShift3.push({{$item->shift}});
        @endforeach
    @endif

    // line chart lancip
    new Chart(document.getElementById("line-chart-lancip"), {
        type: 'line',
        data: {
            labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
            datasets: [{
                data: keluhanGpShift1,
                borderColor: "#00AE4D",
                label: "Shift 1",
                fill: false,
                lineTension: 0,
            }, {
                data: keluhanGpShift2,
                borderColor: "#FAAE32",
                label: "Shift 2",
                fill: false,
                lineTension: 0,
            }, {
                data: keluhanGpShift3,
                borderColor: "#E14A3A",
                label: "Shift 3",
                fill: false,
                lineTension: 0,
            }]
        },
        options: {
            lineTension: 1,
            legend: {
                display: false
            },
            title: {
                display: true,
                text: 'Komplain GP'
            }
        }
    });
</script>


<script>
//Chart
data_chart=[
    { periode: '2020-02-02', a: 10, b: 10 , c: 170 ,d: 310, e: 480 ,f: 630},
    { periode: '2020-02-03', a: 170,b: 350, c: 500 ,d: 300, e: 290 ,f: 540},
    { periode: '2020-02-04', a: 170,b: 170 , c: 300 ,d: 400, e: 550 ,f: 470},
    { periode: '2020-02-05', a: 460,b: 10 , c: 300 ,d: 250, e: 620 ,f: 290},
    { periode: '2020-02-06', a: 720 ,b: 650, c: 480 ,d: 340, e: 590 ,f: 310},
    { periode: '2020-02-07', a: 290 ,b: 670, c: 480 ,d: 450, e: 390 ,f: 450}
]
data_chart2=[
    { periode: '2020-02-02', a: 600, b: 400 },
    { periode: '2020-02-03', a: 530,b: 350},
    { periode: '2020-02-04', a: 500,b: 370 },
    { periode: '2020-02-05', a: 800,b: 1000 },
    { periode: '2020-02-06', a: 720 ,b: 650},
    { periode: '2020-02-07', a: 490 ,b: 670},
    { periode: '2020-02-07', a: 390 ,b: 570}
]

</script>

<!-- chart-line -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Periode');
    data.addColumn('number', 'Produk ZA');
    data.addColumn('number', 'Produk Urea');
    data.addColumn('number', 'Produk SP-36');
    data.addColumn('number', 'Produk Phonska');
    data.addColumn('number', 'Produk Phonska Plus');
    data.addColumn('number', 'Gudang NPX Kebomas');
    data.addRows([
    
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    
   
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45 },
    pointSize: 3,
    
};

var chart = new google.visualization.LineChart(document.getElementById('jenisproduk'));

chart.draw(data, options);
}
</script>

<!-- chart-line -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
data.addColumn('string', 'Periode');
    data.addColumn('number', 'Gudang ZA');
    data.addColumn('number', 'Gudang Urea 1A');
    data.addColumn('number', 'Gudang PF 1');
    data.addColumn('number', 'Gudang Phonska');
    data.addColumn('number', 'Gudang Urea 1B');
    data.addColumn('number', 'Gudang Multiguna');
    data.addRows([
    
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    
   
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45 },
    pointSize: 3,
    
};

var chart = new google.visualization.LineChart(document.getElementById('gudang'));

chart.draw(data, options);
}
</script>

<!-- chart-bar -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Periode');
    data.addColumn('number', 'Gudang ZA');
    data.addColumn('number', 'Gudang Urea 1A');
    data.addColumn('number', 'Gudang PF 1');
    data.addColumn('number', 'Gudang Phonska');
    data.addColumn('number', 'Gudang Urea 1B');
    data.addColumn('number', 'Gudang Multiguna');
    data.addRows([
    
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    
   
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45 },
    
    
};

var chart = new google.visualization.BarChart(document.getElementById('produkrusak'));

chart.draw(data, options);
}
</script>

<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Jumlah');
    data.addColumn('number', 'Produksi');
    data.addColumn('number', 'Pengeluaran');
    data.addRows([
    
    ['2020-02-02',  600, 400 ],
    ['2020-02-03',  530, 350],
    ['2020-02-04',  500, 370 ],
    ['2020-02-05',  800, 1000 ],
    ['2020-02-06',  720 , 650],
    ['2020-02-07',  490 , 670],
    ['2020-02-07',  390 , 570],
    
   
]);


var options = {
    colors: ['#FFC201','#28DAC6'],
    legend:{position: 'bottom'},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' }
};

var chart = new google.visualization.ColumnChart(document.getElementById('produksipengeluaran'));

chart.draw(data, options);
}
</script>

<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Jumlah');
    data.addColumn('number', 'Kapasitas Muat Buruh');
    data.addColumn('number', 'Realisasi Muat');
    data.addRows([
    
    ['2020-02-02',  600, 400 ],
    ['2020-02-03',  530, 350],
    ['2020-02-04',  500, 370 ],
    ['2020-02-05',  800, 1000 ],
    ['2020-02-06',  720 , 650],
    ['2020-02-07',  490 , 670],
    ['2020-02-07',  390 , 570],
    
   
]);


var options = {
    colors: ['#FD7F0C','#1ACA98'],
    legend:{position: 'bottom'},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45 }
};

var chart = new google.visualization.ColumnChart(document.getElementById('muatan'));

chart.draw(data, options);
}
</script>

<script>

//Initialize and add the map
// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: -25.344, lng: 131.036};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 4, center: uluru});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, map: map});
}

    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <!-- <script src="//maps.google.com/maps/api/js?key=AIzaSyBTGnKT7dt597vo9QgeQ7BFhvSRP4eiMSM" type="text/javascript"></script> -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXEzlW0kgiUBH1C7-UrqIezWuUXdsIugc&callback=initMap">
</script>


<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Periode');
    data.addColumn('number', 'Gudang ZA');
    data.addColumn('number', 'Gudang Urea 1A');
    data.addColumn('number', 'Gudang PF 1');
    data.addColumn('number', 'Gudang Phonska');
    data.addColumn('number', 'Gudang Urea 1B');
    data.addColumn('number', 'Gudang Multiguna');
    data.addRows([
    
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    
   
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 } },
    hAxis: { slantedText:true, slantedTextAngle:45 },
    
    
};

var chart = new google.visualization.ColumnChart(document.getElementById('realisasialatberat'));

chart.draw(data, options);
}
</script>

<!-- chart-pie -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = google.visualization.arrayToDataTable([
    ['Laporan', 'Jumlah'],
    ['Ban bocor',     5],
    ['Kedisiplinan Operator',      7],
    ['Kantong produk rusak',  8],
    ['Staple roboh',     5],
    ['Terplas rusak',     13],
    ['Rem rusak',      5],
    ['Oli bocor',  7],
    ['Merusak Pilar Gudang',  8],
   
]);

var yearPattern = "0";
  var formatNumber = new google.visualization.NumberFormat({
    pattern: 'decimal', 
    prefix: 'Rp.'
  });
  formatNumber.format(data, 1);

var options = {
    colors: ['#0FA3BA','#FFC201','#5767DE','#FD367B','#FD7F0C','#007CFF','#00AF4C','#28DAC6'],
    legend:{position: 'bottom', maxTextLines:4},
};

var chart = new google.visualization.PieChart(document.getElementById('keluhanmuatan'));

chart.draw(data, options);
}
</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
          ['Gudang', 'Pakai & Dasaran', 'Kosong ', 'Rusak', 'Total Stok'],
          ['Gudang ZA',  165,      938,         522,             998],
          ['Gudang Urea IA',  135,      1120,        599,             1268],
          ['Gudang PF I',  157,      1167,        587,             1207],
          ['Gudang Phonska',  139,      1010,        615,             1068],
          ['Gudang Urea IB',  136,      691,         629,             1026],
          ['Gudang Multiguna',  135,      1120,        599,             1268],
        ]);

        var options = {
          
          colors: ['#FFC201','#28DAC6','#FD7F0C','#00AF4C'],
          seriesType: 'bars',
          series: {3: {type: 'line'}}  ,
          legend:{position: 'bottom'}      
        };

        var chart = new google.visualization.ComboChart(document.getElementById('stokpaletbulan'));
        chart.draw(data, options);
      }
    </script>

    <script>
    // Class definition
var KTSelect2 = function() {
    // Private functions
    var demos = function() {
        // basic
        $('#pilih_shift').select2({
            placeholder: "Pilih Shift"
        });
        $('#pilih_gudang').select2({
            placeholder: "Pilih Gudang"
        });       
    }

    

    // Public functions
    return {
        init: function() {
            demos();
            
        }
    };
}();

// Initialization
jQuery(document).ready(function() {
    KTSelect2.init();
});


// Class definition

var KTBootstrapDaterangepicker = function () {
    
    // Private functions
    var demos = function () {
        // minimum setup
        // $('#kt_daterangepicker_1').daterangepicker({
        //     buttonClasses: ' btn',
        //     applyClass: 'btn-primary',
        //     cancelClass: 'btn-secondary'
        // });
        $('#kt_daterangepicker_2').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary'
        }, function(start, end, label) {
            $('#kt_daterangepicker_2 .form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        });

    }
    return {
        // public functions
        init: function() {
            demos(); 
            
        }
    };
}();

jQuery(document).ready(function() {
    KTBootstrapDaterangepicker.init();
});
</script>