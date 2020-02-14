@extends('layout.app')

@section('title', 'Laporan LogSheet')

@section('content')

<script>
    document.getElementById('report-laporan-stok-nav').classList.add('kt-menu__item--active');
</script>


<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <form action="{{url('report/log-sheet')}}" method="GET">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Laporan LogSheet
                </h4>
                <p class="sub">
                    Berikut ini adalah form report logsheet pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
        </div>
        <div class="kt-portlet__body">
            <label class="boldd uppercase">Report Builder</label>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Gudang</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="gudang" name="gudang" style="width: 100%">
                        <option></option>
                        @foreach ($gudang as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Shift</h4>
                <div class="col-6">
                    <select class="form-control m-select2" id="shift" name="shift" style="width: 100%">
                        <option></option>
                        @foreach ($shift as $item)
                            <option value="{{$item->id}}">{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Produk</h4>
                <div class="col-6">
                    <div class="mt1" id="textadd">
                        <select class="form-control m-select2" id="pilih" name="pilih_produk" style="width:100%">
                            <option></option>
                            @foreach ($produk as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <h4 class="col-2 col-form-label text-kiri">Tanggal</h4>
                <div class="col-6">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="start_date" name="tanggal" readonly placeholder="Pilih tanggal">
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
                        {{-- <a href="{{asset('assets/reports/stok/stok.xlsx')}}" class="btn btn-success" download> <i class="fa fa-print"></i> Cetak Laporan</a> --}}
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
        placeholder: "Pilih Gudang",
        allowClear: true
    });
    $('#pilih').select2({
        placeholder: "Pilih Produk",
        allowClear: true
    });
    $('#shift').select2({
        placeholder: "Pilih Shift",
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
        $("#Produk").val('').trigger('change');
    } else {
        text.style.display = "block";
    }
}


</script>


@stop