@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')

<script>
    $('body').addClass("kt-aside--minimize");
    document.getElementById('log-aktivitas-user-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Log Aktivitas User
                </h4>
                <p class="sub">
                    Berikut ini adalah data log aktivitas setiap user yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            {{-- <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                        <select class="form-control m-select2 col-12" style="width:200px" id="selcWil" name="param">
                            <option value="" selected disabled>Pilih Wilayah</option>
                            <option value="1">Wilayah 1</option>
                            <option value="2">Wilayah 2</option>
                        </select>
                    <a href="#" class="btn btn-success" data-toggle="modal"
                         onclick="carWil()" style="width:70px">OK</a>
                </div>
            </div> --}}
        </div>
        <div class="kt-portlet__body">
            <div class="col-12">
                <div class="form-group row mt2" style="margin-bottom: 0;">
                    <h4 class="col-form-label text-kiri">Tanggal Awal <span class="text-danger">*</span></h4>
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="start_date" name="tgl_awal" readonly
                                placeholder="Pilih tanggal" onchange="pilih()">
                        </div>
                    </div>
                    <h4 class="col-form-label text-kiri">Tanggal Akhir <span class="text-danger">*</span></h4>
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="end_date" name="tgl_akhir" readonly
                                placeholder="Pilih tanggal" onchange="pilih()">
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr class="text-center">
                        <th scope="col">No</th>
                        <th scope="col">Nama User</th>
                        <th scope="col">Aktivitas</th>
                        <th scope="col">Waktu</th>
                    </tr>
                </thead>
                <tbody class="text-center">

                </tbody>
            </table>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->

<script src="{{asset('assets/extends/js/page/logAktivitasUser.js')}}" type="text/javascript"></script>
<script>
$('#selcWil').select2({
    placeholder: "Pilih Wilayah",
});

$('#start_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    orientation: "bottom left",
    clearBtn:true,
});
$('#end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    orientation: "bottom left",
    clearBtn:true,
});
</script>
@endsection