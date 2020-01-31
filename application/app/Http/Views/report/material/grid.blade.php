@extends('layout.app')

@section('title', 'Laporan Material')

@section('content')

<script>
    document.getElementById('report-laporan-material-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan Transaksi Material
                </h4>
                <p class="sub">
                    Berikut ini adalah form report transaksi material pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
        </div>
        <form action="{{url('report/material')}}" method="GET">
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row mt2">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2 @error('title') is-invalid @enderror" id="gudang" name="gudang[]" multiple="multiple" style="width: 100%">
                        @foreach ($gudang as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Material</h4>
                <div class="col-6">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--success">
                            <input id="semuaCheck" type="radio" name="material" value="1" onclick="checkSemua()"> Semua
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--warning">
                            <input id="myCheck" type="radio" name="material" value="2" onclick="checkBx()"> Spesifik
                            <span></span>
                        </label> 
                    </div>
                    <div class="mt1" id="textadd" style="display:none;">
                        <select class="form-control m-select2" id="material" name="pilih_material[]" multiple="multiple" style="width:100%">
                            @foreach ($produk as $item)
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
                                    <label>Start Date</label>
                                    <input type="text" class="form-control" id="start_date" name="tgl_awal" readonly
                                        placeholder="Pilih tanggal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" class="form-control" id="end_date" name="tgl_akhir" readonly
                                        placeholder="Pilih tanggal">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        {{-- <a href="{{asset('assets/reports/material/material.xlsx')}}" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</a> --}}
                        <button type="submit" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    $('#gudang').select2({
        placeholder: "Pilih gudang"
    });
    $('#material').select2({
        placeholder: "Pilih material",
        allowClear: true
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
        $("#material").val('').trigger('change');
    } else {
        text.style.display = "block";
    }
}
</script>


@stop