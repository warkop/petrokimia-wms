@extends('layout.app')

@section('title', 'Tambah Rencana Harian')

@section('content')

@section('content')


<!-- begin:: Content -->
<form id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <input type="hidden" class="form-control" id="id" name="id">
    <input type="hidden" name="action" id="action" value="add">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Tambah Data Rencana Harian
                </h4>
                <p class="sub">
                    Berikut ini adalah tambah data rencana harian pada <span class="text-ungu kt-font-bolder">Aplikasi
                        WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">

                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" class="form-control" disabled value="{{$tanggal ?? date('d/m/Y')}}">
                    </div>
                    <div class="form-group">
                        <label>Shift Kerja</label>
                        <select class="form-control kt-selectpicker" id="id_shift" name="id_shift">
                            @foreach ($shift_kerja as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>    
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin Loket</label>
                        <select class="form-control m-select2" id="admin_loket" name="admin_loket[]" multiple="multiple">
                            @foreach ($admin_loket as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>    
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Operator Alat Berat</label>
                        <select class="form-control m-select2" id="op_alat_berat" name="op_alat_berat[]" multiple="multiple">
                            @foreach ($op_alat_berat as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>    
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alat Berat</label>
                        <select class="form-control m-select2" id="alat_berat" name="alat_berat[]" multiple="multiple">
                            @foreach ($alat_berat as $item)
                                <option value="{{$item->id}}">{{$item->nomor_lambung}}</option>    
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Checker</label>
                        <select class="form-control m-select2" id="checker" name="checker[]" multiple="multiple">
                            @foreach ($checker as $item)
                                <option value="{{$item->id}}">{{$item->nama}}</option>    
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table" id="table_housekeeper">
                        <thead>
                            {{-- <th width="10%">No</th> --}}
                            <th width="30%">Nama Housekepper</th>
                            <th>Area Kerja</th>
                            <th width="10%"><button type="button" class="btn btn-success btn-sm btn-block" onclick="tambahHouseKeeper()"><i class="fa fa-plus"></i> Tambah</button></th>
                        </thead>
                        <tbody>
                            {{-- <tr>
                                <td>1</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple" >
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Maya</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_11" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr> --}}
                            {{-- <tr>
                                <td>2</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple">
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Suryati</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_12" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <select class="form-control m-select2 kt_select2_housekeeping" style="width: 100% !important" name="param" multiple="multiple">
                                        <option value="AK">Suryati</option>
                                        <option value="HI">Suryati</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control m-select2" id="kt_select2_13" name="param" multiple="multiple">
                                        <option>Pilih area</option>
                                        <option>Area A</option>
                                        <option>Area B</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-block"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto">
                        <button type="button" class="btn btn-wms btn-elevate btn-elevate-air ladda-button" data-style="zoom-in"  id="btn_save"><i class="la la-save"></i> Simpan Data</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--End::Dashboard 6-->
</div>
</form>
<!-- end:: Content -->





<script src="{{asset('assets/extends/js/page/rencana-harian.js')}}" type="text/javascript"></script>
<script>
    $('.kt-selectpicker').selectpicker();
    $('#kt_select2_3').select2({
        placeholder: "Select admin gudang",
    });
    $('#admin_loket').select2({
        placeholder: "Select admin loket",
    });
    $('#op_alat_berat').select2({
        placeholder: "Select operator alat berat",
    });
    $('#alat_berat').select2({
        placeholder: "Select alat berat",
    });
    $('#checker').select2({
        placeholder: "Select checker",
    });


    $('#kt_select2_11').select2({
        placeholder: "Select area kerja",
    });

    $('#kt_select2_12').select2({
        placeholder: "Select area kerja",
    });

    $('#kt_select2_13').select2({
        placeholder: "Select area kerja",
    });

    $('#kt_select2_1, #kt_select2_operator, #kt_select2_loket, #kt_select2_checker').select2({
        placeholder: "Select Alat Berat"
    });
    $('.kt_select2_housekeeping').select2({
        placeholder: "Select Housekepping"
    });
    $('#end_date').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        clearBtn:true,
        orientation: "top left"
    });

    @if (!empty($id)) 
        edit({{$id}});
        @foreach ($tkbm_rencana as $item)
            tambahHouseKeeper({{$item->id_rencana}}, {{$item->id_tkbm}});
        @endforeach
    @endif
</script>
@endsection