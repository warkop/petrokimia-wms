@extends('layout.app')

@section('title', 'Rencana Harian')

@section('content')

<script>
    document.getElementById('report-realisasi-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan Realisasi
                </h4>
                <p class="sub">
                    Berikut ini adalah form report realisasi pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
        </div>
        <form action="{{url('report/realisasi')}}" method="GET">
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
                <h4 class="col-2 col-form-label text-kiri">Produk</h4>
                <div class="col-6">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--success">
                            <input id="semuaCheck" type="radio" name="produk" value="1" onclick="checkSemua()"> Semua
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--warning">
                            <input id="myCheck" type="radio" name="produk" value="2" onclick="checkBx()"> Spesifik
                            <span></span>
                        </label> 
                    </div>
                    <div class="mt1" id="textadd" style="display:none;">
                        <select class="form-control m-select2" id="produk" name="pilih_produk[]" multiple="multiple" style="width:100%">
                            @foreach ($produk as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Shift</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="shift" name="shift[]" multiple="multiple" style="width: 100%">
                        @foreach ($shift as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Kegiatan</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="kegiatan" name="kegiatan[]" multiple="multiple" style="width: 100%">
                         @foreach ($aktivitas as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
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
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="offset-lg-2">
                        <button type="submit" class="btn btn-success" download=""> <i class="fa fa-print"></i> Cetak Laporan</a>
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
    $('#produk').select2({
        placeholder: "Pilih produk",
        allowClear: true
    });
    $('#shift').select2({
        placeholder: "Pilih shift"
    });
    $('#kegiatan').select2({
        placeholder: "Pilih kegiatan"
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
        $("#produk").val('').trigger('change');
    } else {
        text.style.display = "block";
    }
}
</script>


@stop