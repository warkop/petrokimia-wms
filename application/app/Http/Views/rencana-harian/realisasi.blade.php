@extends('layout.app')

@section('title', 'Tambah Rencana Harian')

@section('content')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <form id="form1" onsubmit="return false">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-title">
                    <h4 class="kt-portlet__head-text title_sub pt-4">
                        {{-- <i class="la la-group"></i> &nbsp; --}}
                        Realisasi Rencana Harian
                    </h4>
                    <p class="sub">
                        Berikut ini adalah form realisasi rencana harian pada <span
                            class="text-ungu kt-font-bolder">Aplikasi
                            WMS Petrokimia.</span>
                    </p>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group pt-4">

                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row border-bottom mb3">
                    <div class="col-12 mb1">
                        <button class="btn btn-warning btn-sm  pull-right" onclick="tambahMaterial()"> Tambah Material</button>
                    </div>
                    <div class="col-md-12">
                        <table class="table" id="table_material">
                            <thead class="text-center">
                                <th scope="col" width="35%">Material</th>
                                <th scope="col" width="20%">Bertambah</th>
                                <th scope="col" width="20%">Berkurang</th>
                                <th scope="col" width="5%"></th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="kel">
                    <div class="row">
                        <div class="col-6 mb1">
                            <h4 class="mb2">Housekeeper</h4>
                        </div>
                        <div class="col-6 mb1">
                            <button class="btn btn-warning btn-sm  pull-right" onclick="tambahHousekeeper()"> Tambah Housekeeper</button>
                        </div>
                        <div id="table_housekeeper">
                            {{-- <div class="baris">
                                <div class="col-3">
                                    <label class="boldd-500">Pilih Housekeeper</label>
                                    <select class="form-control m-select2 kt_select2_housekeeping" id="housekeeper-1" name="housekeeper" aria-placeholder="Pilih Housekeeper" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="col-9 col-form-label">
                                    <label class="boldd-500" style="transform: translateY(-.6rem);">Pilih Area Kerja</label>
                                    <div class="col-12">
                                        <div class="row form-group mb-0 mb2">
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 1
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 2
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 3
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 4
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 5
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 6
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 7
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 5
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 6
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 7
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                    <input type="checkbox"> Area 5
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="col-2 mb1 text-left">
                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#kt_modal_1"> Tambah Area</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-12 ml-lg-auto text-right">
                            <button type="button" id="btn_save" class="btn btn-wms btn-elevate btn-elevate-air ladda-button" data-style="zoom-in"><i class=" la la-save"></i>
                                Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->



<!--begin::Modal-->
<div class="modal fade" id="kt_modal_1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            {{-- <form action=""> --}}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="id_row">
                        <div class="form-group">
                            <label for="kt_select2_gudang" class="">Gudang</label>
                            <select class="form-control m-select2" id="kt_select2_gudang" onchange="getArea()" name="gudang" style="width: 100%">
                                <option value=""></option>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="">List Area</label>
                            <select class="form-control m-select2" id="kt_select2_area" name="param" multiple="multiple" style="width:100%;">
                               
                            </select>
                            <span class="form-text text-muted">* Anda dapat memilih lebih dari satu area.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="pilihArea()">Tambah Area </button>
            </div>
            {{-- </form> --}}
        </div>
    </div>
</div>

<!--end::Modal-->



<script src="{{asset('assets/extends/js/page/realisasi.js')}}" type="text/javascript"></script>
<script>
$('.kt-selectpicker').selectpicker();
$('#kt_select2_3').select2({
    placeholder: "Select admin gudang",
});

$('#HK-1').select2({
    placeholder: "Select alat berat",
});

$('#kt_select2_1, #kt_select2_operator, #kt_select2_loket, #kt_select2_checker').select2({
    placeholder: "Select Alat Berat"
});
// $('.kt_select2_housekeeping').select2({
//     placeholder: "Select Housekepping"
// });
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "top left"
});

$('#kt_select2_gudang').select2({
    placeholder: "Select gudang",
});
$('#kt_select2_area').select2({
    placeholder: "Select area",
});

const id_rencana = "{{ $id_rencana }}";

@if (!empty($store_material))
    @foreach($store_material as $item)
        tambahMaterial({{$item->id_realisasi}}, {{$item->id_material}}, {{$item->bertambah}}, {{$item->berkurang}});
    @endforeach
@endif

@if (!empty($store_housekeeper))
    @foreach($store_housekeeper as $item)
        tambahHousekeeper({{$item->id_tkbm}});
    @endforeach
@endif

@php 
$no=1;
@endphp
@if (!empty($store_area_housekeeper))
    // $("#id_row").val({{count($store_area_housekeeper)}});
    @foreach ($store_area_housekeeper as $item)
        setArea({{$item->id_tkbm}}, {{$item->id_area}}, "{{$item->nama}}");
    @php
        $no++;
    @endphp
    @endforeach
@endif
</script>
@endsection