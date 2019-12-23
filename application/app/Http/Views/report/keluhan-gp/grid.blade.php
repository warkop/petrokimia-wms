@extends('layout.app')

@section('title', 'Rencana Harian')

@section('content')

<script>
    document.getElementById('report-keluhan-gp-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Laporan Keluhan GP
                </h4>
                <p class="sub">
                    Berikut ini adalah form report keluhan gp pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            {{-- <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="{{url('/add-rencana-harian')}}" class="btn btn-wms btn-elevate btn-elevate-air"><i
                class="la la-plus"></i> Tambah Data</a>
        </div>
    </div> --}}
        </div>
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri mt2">Periode</h4>
                <div class="col-6">
                    <div class="kel-min">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" class="form-control" id="start_date" name="start_date" readonly
                                        placeholder="Pilih tanggal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" class="form-control" id="end_date" name="end_date" readonly
                                        placeholder="Pilih tanggal">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="gudang" name="param" multiple="multiple" style="width: 100%">
                        <option value="aa">Gudang A</option>
                        <option value="AK">Gudang B</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Keluhan</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="keluhan" name="param" multiple="multiple" style="width: 100%">
                        <option value="aa">Pupuk Basah</option>
                        <option value="AK">Pupuk Kering</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Kegiatan</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="kegiatan" name="param" multiple="multiple" style="width: 100%">
                        <option value="aa">Produksi</option>
                        <option value="AK">Pengiriman Pemindahan</option>
                        <option value="a">Geser Area</option>
                        <option value="b">Bongkar Muat Truk Kembali</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Produk</h4>
                <div class="col-6">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--success">
                            <input id="semuaCheck" type="radio" name="radio2" onclick="checkSemua()"> Semua
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--warning">
                            <input id="myCheck" type="radio" name="radio2" onclick="checkBx()"> Spesifik
                            <span></span>
                        </label> 
                    </div>
                    <div class="mt1" id="textadd" style="display:none;">
                        <select class="form-control m-select2" required name="produk" id="produk" name="param" multiple="multiple" style="width:100%">
                            <option value="xx" disabled>Pilih produk</option>
                            <option value="aa">Pallet Plastik</option>
                            <option value="AK">Terplas</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        <a href="{{asset('assets/reports/keluhan-gp/keluhan-gp.xlsx')}}" class="btn btn-success" download> <i class="fa fa-print"></i> Cetak Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#gudang').select2({
        placeholder: "Pilih gudang penyangga"
    });
    $('#produk').select2({
        placeholder: "Pilih produk",
        allowClear: true
    });
    $('#keluhan').select2({
        placeholder: "Pilih keluhan"
    });
    $('#kegiatan').select2({
        placeholder: "Pilih kegiatan"
    });
    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        orientation: "bottom left"
    });

function checkBx() {
  var checkBox = document.getElementById("myCheck");
  var text = document.getElementById("textadd");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}

function checkSemua() {
    var checkBox = document.getElementById("semuaCheck");
    var text = document.getElementById("textadd");
    if (checkBox.checked == true){
        text.style.display = "none";
        $("#produk").val('').trigger('change');
        $("#produk").attr(
            "data-placeholder","Pilih produk"
        );
    }
}
</script>


@stop