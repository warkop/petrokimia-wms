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

<div class="kt-content  kt-grid__item kt-grid__item--fluid" style="margin-bottom: -8vh">
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
</div>


<div class="kt-content  kt-grid__item kt-grid__item--fluid">
    <!--Begin::Dashboard 6-->
    <div class="row">
        <div class="col-9">
            <div class="kt-portlet">
                <div class="kt-portlet__head no-border-bottom">
                    <div class="kt-portlet__head-title">
                        <h5 class="kt-portlet__head-text title_sub pt-4">
                            {{-- <i class="la la-group"></i> &nbsp; --}}
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
                    <canvas id="line-chart" width="800" height="310"></canvas>
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
            labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
            datasets: [{
                data: [86, 100, 106, 20, 107, 24, 133],
                label: "Shift 1, Alat Berat: Rp 1.000.000, SDM: Rp 800.000",
                // label: "Shift 6",
                borderColor: "#00AE4D",
                fill: false
            }, {
                data: [1, 150, 411, 202, 135, 309, 247],
                label: "Shift 2, Alat Berat: Rp 700.000, SDM: Rp 300.000",
                borderColor: "#FAAE32",
                fill: false
            }, {
                data: [68, 50, 78, 90, 203, 176, 408],
                label: "Shift 3, Alat Berat: Rp 3.000.000, SDM: Rp 1.800.000",
                borderColor: "#E14A3A",
                fill: false
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Ton per Shift'
            }
        }
    });



    // chart bar 
    new Chart(document.getElementById("bar-chart"), {
        type: 'bar',
        data: {
            labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
            datasets: [{
                data: [318, 150, 278, 490, 103, 176, 108],
                backgroundColor: "#00AE4D",
                label: "Shift 1",
                fill: false
            }, {
                data: [178, 150, 378, 390, 66, 376, 208],
                backgroundColor: "#FAAE32",
                label: "Shift 2",
                fill: false
            }, {
                data: [168, 200, 78, 290, 103, 276, 308],
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


    // line chart lancip
    new Chart(document.getElementById("line-chart-lancip"), {
        type: 'line',
        data: {
            labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
            datasets: [{
                data: [86, 100, 106, 20, 107, 24, 133],
                borderColor: "#00AE4D",
                label: "Shift 1",
                fill: false,
                lineTension: 0,
            }, {
                data: [10, 150, 411, 202, 135, 309, 247],
                borderColor: "#FAAE32",
                label: "Shift 2",
                fill: false,
                lineTension: 0,
            }, {
                data: [68, 50, 78, 90, 203, 176, 408],
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

