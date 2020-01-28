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


<div class="row" style="padding: 2em 2em 0 2em;">
    <div class=" offset-7 col-3">
        <a href="{{url('main')}}" class="btn btn-success btn-pill pull-right btn-md">
            <span>
                <i class="fa fa-arrow-left"></i>
                <span class="boldd">
                    Kembali ke Halaman Depan
                </span>
            </span>
        </a>
    </div>
    <div class=" col-2">
        <a href="{{url('layout')}}" class="btn btn-wms btn-pill pull-right btn-md">
            <span>
                <i class="fa fa-arrow-left"></i>
                <span class="boldd">
                    Kembali ke Menu
                </span>
            </span>
        </a>
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
    <div class="row">
        <div class="col-9">
            <div class="kt-portlet">
                <div class="kt-portlet__head no-border-bottom">
                    <div class="kt-portlet__head-title">
                        <h5 class="kt-portlet__head-text title_sub pt-4">
                            <div class="row">
                                <label class="col-form-label">Periode 1</label>
                                <div class="col-4">
                                    <input class="form-control" id="kt_datepicker_1" readonly placeholder="Pilih periode mulai"
                                        type="text" />
                                </div>
                                <label class="offset-1 col-form-label">Periode 2</label>
                                <div class="col-4">
                                    <input class="form-control" id="kt_datepicker_2" readonly
                                        placeholder="Pilih periode selesai" type="text" />
                                </div>
                            </div><br>
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
                    <canvas id="line-chart" width="800" height="510"></canvas>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head no-border-bottom">
                            <div class="kt-portlet__head-title">
                                <h5 class="kt-portlet__head-text title_sub pt-4">
                                    Shift 1
                                </h5>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-group pt-4">
                                    {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <h2 class="contains-chart-result" style="color:#00AE4D">600 Ton</h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head no-border-bottom">
                            <div class="kt-portlet__head-title">
                                <h5 class="kt-portlet__head-text title_sub pt-4">
                                    Shift 2
                                </h5>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-group pt-4">
                                    {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <h2 class="contains-chart-result" style="color:#FAAE32">700 Ton</h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head no-border-bottom">
                            <div class="kt-portlet__head-title">
                                <h5 class="kt-portlet__head-text title_sub pt-4">
                                    Shift 3
                                </h5>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-group pt-4">
                                    {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Data</a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <h2 class="contains-chart-result" style="color:#E14A3A">400 Ton</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class=" kt-content  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-6">
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
        <div class="col-6">
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
</div>


@include('layout.footer')
<script src="{{('assets/extends/js/page/dashboard.js')}}" defer></script>

<script>
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
            title: {
                display: true,
                text: 'Ton per Shift'
            }
        }
    });

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

