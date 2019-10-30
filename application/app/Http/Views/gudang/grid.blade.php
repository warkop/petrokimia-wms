@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')

<script>
    $('body').addClass("kt-aside--minimize");
    document.getElementById('gudang-nav').classList.add('kt-menu__item--active');
</script>
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Gudang
                </h4>
                <p class="sub">
                    Berikut ini adalah data gudang untuk menyimpan alat berat yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal"
                         onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Id Sloc</th>
                        <th>Id Plant</th>
                        <th>Nama Gudang</th>
                        <th>Tipe Gudang</th>
                        <th>Min Pallet</th>
                        {{-- <th>Min Terplas</th> --}}
                        {{-- <th>Jumlah Pupuk</th> --}}
                        {{-- <th>Jumlah Alat Berat</th> --}}
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->


<!--begin::Modal-->
<div class="modal fade btn_close_modal" id="modal_form" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <input type="hidden" name="action" id="action" value="add">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Id Sloc</label>
                                <input type="text" class="form-control number-only" name="id_sloc" id="id_sloc" placeholder="Masukkan id sloc">
                            </div>
                            <div class="form-group">
                                <label>Id Plant</label>
                                <input type="text" class="form-control number-only" name="id_plant" id="id_plant" placeholder="Masukkan id plant">
                            </div>
                            <div class="form-group">
                                <label>Nama Gudang</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama gudang">
                            </div>
                            <div class="form-group">
                                <label for="exampleSelect1">Pilih Gudang</label>
                                <select class="form-control" id="tipe_gudang" name="tipe_gudang">
                                    <option value="1">Internal</option>
                                    <option value="2">Eksternal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kepala Regu</label>
                                <select class="form-control m-select2" id="id_karu" name="id_karu" aria-placeholder="Pilih Kepala Regu" style="width: 100%;">
                                    <option value="">Pilih Kepala Regu</option>
                                    @foreach ($karu as $item)
                                        <option value="{{$item->id}}">{{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="boldd-500">Jumlah Minimal Palet</label>
                            @php $i=0 @endphp
                            @foreach ($material as $item)
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label class="col-form-label">{{$item->nama}}</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="hidden" class="form-control" id="id-material-{{$item->id}}" name="material[]" value="{{$item->id}}">
                                        <input type="text" class="form-control material" id="stok-min-{{$item->id}}" name="stok_min[]" placeholder="Masukkan minimal {{$item->nama}}">
                                    </div>
                                </div>
                                @php $i++ @endphp
                            @endforeach
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="text" class="form-control input-enter" id="start_date" name="start_date" readonly placeholder="Select date" value="{{date('d-m-Y')}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="text" class="form-control input-enter" id="end_date" name="end_date" readonly placeholder="Select date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in" id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal jumlah pupuk -->
<div class="modal fade" id="kt_modal_pupuk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Jumlah Pupuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pupuk</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>Pupuk Urea</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">2</td>
                                        <td>Pupuk ZA</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">3</td>
                                        <td>Pupuk SP-36</td>
                                        <td>80</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">4</td>
                                        <td>Pupuk Rock Phospate</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">5</td>
                                        <td>Petro Nitrat</td>
                                        <td>10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal jumlah alat berat -->
<div class="modal fade" id="kt_modal_alat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Jumlah Alat Berat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori Alat Berat</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>Excavator</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">2</td>
                                        <td>Alat Pengangkut (Truk)</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">3</td>
                                        <td>Crane</td>
                                        <td>80</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">4</td>
                                        <td>Compactor</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">5</td>
                                        <td>Forklift</td>
                                        <td>10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->





<script src="{{asset('assets/extends/js/page/gudang.js')}}" type="text/javascript"></script>
<script>
$("#clear").click(function(){
    $('#end_date').data('datepicker').setDate(null);
});
$('#id_karu').select2({
    placeholder: "Pilih Kepala Regu",
    allowClear: true
});
$('#end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    clearBtn:true,
    orientation: "bottom left"
});
</script>
@endsection