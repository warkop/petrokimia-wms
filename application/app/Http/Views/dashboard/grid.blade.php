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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

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
                            <h5 class="kt-portlet__head-text title_sub pt-4">
                                <br>
                                Dashboard
                                </h4>
                                <p class="sub">
                                    Berikut ini adalah statistk pengiriman barang per-shift pada <span
                                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                                </p>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group pt-4">
                                {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="container row" style="margin-bottom:3rem">
                            <label class="col-form-label col-2 font-weight-bold">Periode 1</label>
                            <div class="col-3">
                                <input class="form-control" id="kt_datepicker_1" readonly placeholder="Pilih periode mulai"
                                    type="text" />
                            </div>
                            <label class="offset-1 col-form-label col-2 font-weight-bold">Periode 2</label>
                            <div class="col-3">
                                <input class="form-control" id="kt_datepicker_2" readonly
                                    placeholder="Pilih periode selesai" type="text" />
                            </div>
                        </div>
                        <div class="container row">
                            <p> 
                                <span class="mr1"> <i class="fa fa-square gd-a-color-1"></i> Gudang Ponska</span>
                                <span class="mr1"> <i class="fa fa-square gd-a-color-2"></i> Gudang Amurea</span>
                                <span class="mr1"> <i class="fa fa-square gd-a-color-3"></i> Gudang Petrocas</span>
                            </p>
                        </div>
                        <div class="row">
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
                        </div>
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
    <div class="row">
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
    </div>

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

