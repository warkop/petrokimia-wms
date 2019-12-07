@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')

<script>
    $('body').addClass("kt-aside--minimize");
    document.getElementById('gudang-nav').classList.add('kt-menu__item--active');
</script>
<link rel="stylesheet" href="{{asset('assets/extends/css/map.css')}}">
<script src="//maps.google.com/maps/api/js?key=AIzaSyBDHDV2ksjKZ8xtSOZEOBe4_DQM87VrXgI" type="text/javascript" defer></script>
<script src="{{aset_tema()}}vendors/custom/gmaps/gmaps.js" type="text/javascript"></script>
<script src="{{aset_tema()}}app/custom/general/components/maps/google-maps.js" type="text/javascript"></script>
{{-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing"></script> --}}
{{-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;libraries=drawing&amp;dummy=.js"></script> --}}
<!-- begin:: Content -->

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Layout Gudang
                </h4>
                <p class="sub">
                    Berikut ini adalah data pemetaan layout gudang yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                        <select class="form-control m-select2 col-12" style="width:200px" id="selcWil" name="param">
                            <option value="" selected disabled>Pilih Area</option>
                            <option value="wil1">Area 1</option>
                            <option value="wil2">Area 2</option>
                            {{-- <option value="drawManual">Draw Manual</option> --}}
                        </select>
                    <a href="#" class="btn btn-success" data-toggle="modal"
                         onclick="carWil()" style="min-width:130px">Mulai Gambar</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="col-md-12 text-center" style="min-height:50vh">
                <div id="layoutGudang" style="height:500px;display:none !important"></div>
                
                <div class="" id="noSelectWil">
                    <img class="text-center"  src="{{asset('assets/extends/img/illustration/wilayah.svg')}}" alt="" srcset="" style="width: 30vh;margin-top: 7vh;opacity: .8;"> <br>
                    <label id="labelnoSelectWil" class="boldd text-center" style="margin-top:3vh">Belum ada area yang dipilih</label>
                </div>
            </div>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->

<script src="{{asset('assets/extends/js/page/gudangLayout.js')}}" type="text/javascript"></script>
<script>
// $('#id_karu').select2({
//     placeholder: "Pilih Kepala Regu",
//     allowClear: true,
//     dropdownParent:$("#modal_form")
// });
// $('#end_date').datepicker({
//     rtl: KTUtil.isRTL(),
//     todayHighlight: true,
//     format:'dd-mm-yyyy',
//     clearBtn:true,
//     orientation: "bottom left"
// });
$('#selcWil').select2({
    placeholder: "Pilih Area",
    allowClear: true,
});

$('#selcWil').on('change', function() {
    if ($(this).val()=='wil1') {
        mapsArea1();
    } else if ($(this).val()=='wil2') {
        mapsArea2();
    } else if ($(this).vall()=='drawManual') {
        drawmanual();
    } else {
        alert('NULLLLL');
    }
});


</script>
@endsection