@extends('layout.app')

@section('title', 'Laporan Material')

@section('content')

<script>
    document.getElementById('report-material-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan Material
                </h4>
                <p class="sub">
                    Berikut ini adalah form report material pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
        </div>
        <form id="form-report">
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="gudang" name="gudang[]" multiple="multiple" style="width: 100%">
                        @foreach ($gudang as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Material <span class="text-danger">*</span></h4>
                <div class="col-6">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--success">
                            <input id="check_material_pallet" type="radio" name="material" value="1" onclick="checkBoxMaterialPallet()"> Material pallet
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--warning">
                            <input id="check_material_lain_lain" type="radio" name="material" value="2" onclick="checkBoxMaterialLainlain()"> Material lain-lain
                            <span></span>
                        </label> 
                    </div>
                    <div class="mt1" id="textadd1" style="display:none;">
                        <select class="form-control m-select2" id="material_pallet" name="pilih_material_pallet" style="width:100%">
                            @foreach ($material_pallet as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt1" id="textadd2" style="display:none;">
                        <select class="form-control m-select2" id="material_lain_lain" name="pilih_material_lain_lain[]" multiple="multiple" style="width:100%">
                            @foreach ($material_lain_lain as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Periode</h4>
                <div class="col-6">
                    <div class="kel-min">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="end_date" name="tgl_akhir" readonly
                                        placeholder="Pilih tanggal">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="error-msg"></div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        {{-- <a href="{{asset('assets/reports/produk/produk.xlsx')}}" class="btn btn-success"> <i class="fa fa-print"></i> Cetak Laporan</a> --}}
                        <button type="button" onclick="cetak('material')" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</button>
                        <button type="button" onclick="cetak('material','preview')" class="btn btn-warning" download=""> <i class="fa fa-binoculars "></i> Preview Laporan</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script src="{{asset('assets/extends/js/page/cetak-report.js')}}"></script>
<script>
    $('#gudang').select2({
        placeholder: "Semua gudang"
    });
    $('#material_pallet').select2({
        placeholder: "Pilih Pallet",
    });

    $('#material_lain_lain').select2({
        placeholder: "Semua Material Lain-lain",
        allowClear: true
    });

    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        orientation: "top left"
    });

function checkBoxMaterialPallet() {
  var checkBox = document.getElementById("check_material_pallet");
  var text1 = document.getElementById("textadd1");
  var text2 = document.getElementById("textadd2");
  if (checkBox.checked == true){
    text1.style.display = "block";
    text2.style.display = "none";
  } else {
    text1.style.display = "none";
    text2.style.display = "block";
  }
}

function checkBoxMaterialLainlain() {
    var checkBox = document.getElementById("check_material_lain_lain");
    var text1 = document.getElementById("textadd1");
    var text2 = document.getElementById("textadd2");
    if (checkBox.checked == true){
        text1.style.display = "none";
        text2.style.display = "block";
    } else {
        text1.style.display = "block";
        text2.style.display = "none";
    }
}
</script>


@stop