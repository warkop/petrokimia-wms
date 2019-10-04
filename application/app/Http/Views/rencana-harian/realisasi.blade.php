@extends('layout.app')

@section('title', 'Tambah Rencana Harian')

@section('content')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
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
                <div class="col-md-12">
                    <table class="table">
                        <thead class="text-center">
                            <th width="10%">No</th>
                            <th width="20%">Material</th>
                            <th width="20%">Bertambah</th>
                            <th width="20%">Berkurang</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                                        aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Sapu Ijuk</option>
                                        <option value="HI">Sekop</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah bertambah">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah berkurang">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                                        aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Kemoceng</option>
                                        <option value="HI">Sekop</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah bertambah">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah berkurang">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                                        aria-placeholder="Pilih kategori" style="width: 100%;">
                                        <option value="AK">Trash Bag</option>
                                        <option value="HI">Sekop</option>
                                        <option value="HI">Sapu</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah bertambah">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="" placeholder="Jumlah berkurang">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="kel">
                <h4 class="mb2">House Keeping</h4>
                <div class="row">
                    <div class="col-3">
                        <label class="boldd-500">Pilih House Keeping</label>
                        <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                            aria-placeholder="Pilih House Keeping" style="width: 100%;">
                            <option value="Eman Pradipta">Eman Pradipta</option>
                            <option value="Uli Wibowo">Uli Wibowo</option>
                            <option value="Jayeng Januar">Jayeng Januar</option>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <select class="form-control m-select2 kt_select2_housekeeping" name="param"
                            aria-placeholder="Pilih House Keeping" style="width: 100%;">
                            <option value="Aurora Pudjiastuti">Aurora Pudjiastuti</option>
                            <option value="Balamantri Maryati">Balamantri Maryati</option>
                            <option value="Taufik Susanti">Taufik Susanti</option>
                        </select>
                    </div>
                    <div class="col-9 col-form-label">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto text-right">
                        <a href="#" class="btn btn-wms btn-elevate btn-elevate-air""><i class=" la la-save"></i>
                            Simpan Data</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->





<script src="{{asset('assets/extends/js/page/master-aktivitas.js')}}" type="text/javascript"></script>
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
$('.kt_select2_housekeeping').select2({
    placeholder: "Select Housekepping"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "top left"
});
</script>
@endsection