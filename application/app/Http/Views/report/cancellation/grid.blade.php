@extends('layout.app')

@section('title', 'Laporan Cancellation')

@section('content')

<script>
    document.getElementById('report-cancellation-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan Cancellation
                </h4>
                <p class="sub">
                    Berikut ini adalah form report cancellation pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
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
                <h4 class="col-2 col-form-label text-kiri">Periode <span class="text-danger">*</span></h4>
                <div class="col-6">
                    <div class="kel-min">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Awal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="start_date" name="tgl_awal" readonly
                                        placeholder="Pilih tanggal" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="end_date" name="tgl_akhir" readonly
                                        placeholder="Pilih tanggal" required>
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
                        <button type="button" onclick="cetak('cancellation')" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</button>
                        <button type="button" onclick="cetak('cancellation','preview')" name="preview" value="true" class="btn btn-warning" download=""> <i class="fa fa-binoculars "></i> Preview Laporan</button>
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
        placeholder: "Pilih gudang"
    });

    $('#start_date, #end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        orientation: "top left"
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
        $("#pallet").val('').trigger('change');
    } else {
        text.style.display = "block";
    }
}
</script>


@stop