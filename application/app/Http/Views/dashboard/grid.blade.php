@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
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
                    <div class="row mb5">
                        <label class="col-form-label">Periode 1</label>
                        <div class="col-4">
                            <input class="form-control" id="kt_datepicker_1" readonly placeholder="Pilih periode mulai" type="text" />
                        </div>
                        <label class="offset-1 col-form-label">Periode 2</label>
                        <div class="col-4">
                            <input class="form-control" id="kt_datepicker_2" readonly placeholder="Pilih periode selesai"
                                type="text" />
                        </div>
                    </div>
                    <canvas id="line-chart" width="800" height="450"></canvas>
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

    <!--End::Dashboard 6-->
</div>

<script src="{{('assets/extends/js/page/dashboard.js')}}"></script>

<script>
    // var xy = parseInt("800") ;

    // console.log(xy);



    new Chart(document.getElementById("line-chart"), {
  type: 'line',
  data: {
    labels: [1,2,3,4,5,6,7],
    datasets: [{ 
        data: [86,100,106,20,107,24,133],
        label: "Shift 1, Alat Berat: Rp 1.000.000, SDM: Rp 800.000",
        // label: "Shift 6",
        borderColor: "#00AE4D",
        fill: false
      }, { 
        data: [1,150,411,202,135,309,247],
        label: "Shift 2, Alat Berat: Rp 700.000, SDM: Rp 300.000",
        borderColor: "#FAAE32",
        fill: false
      }, { 
        data: [68,50,78,90,203,176,408],
        label: "Shift 3, Alat Berat: Rp 3.000.000, SDM: Rp 1.800.000",
        borderColor: "#E14A3A",
        fill: false
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'Ton per Shift'
    }
  }
});


</script>


@stop