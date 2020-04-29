@extends('layout.app')

@section('title', 'List Pallet')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!-- begin:: Widget -->
    <div class="row">
        <div class="col-lg-3 col-md-3" onclick="showPallet(1)" style="cursor: pointer">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-green bg-green-custom" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">{{$stok->total??0}}</span>
                            <span class="kt-widget26__desc" style="color: white;">Pallet Stok</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3" onclick="showPallet(2)" style="cursor: pointer">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-green bg-green-custom" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">{{$dipakai->total??0}}</span>
                            <span class="kt-widget26__desc" style="color: white;">Pallet Dipakai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3" onclick="showPallet(3)" style="cursor: pointer">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-green" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">{{$kosong->total??0}}</span>
                            <span class="kt-widget26__desc">Pallet Kosong</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3" onclick="showPallet(4)" style="cursor: pointer">
            <div class="kt-portlet kt-portlet--height-fluid-half kt-portlet--border-bottom-green" style="height: 150px;">
                <div class="kt-portlet__body kt-portlet__body--fluid">
                    <div class="kt-widget26">
                        <div class="kt-widget26__content">
                            <span class="kt-widget26__number">{{$rusak->total??0}}</span>
                            <span class="kt-widget26__desc">Pallet Rusak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Widget -->

    <div class="row">
        <div class="col-lg-12">
            <!--Begin::Dashboard 6-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-title">
                        <h4 class="kt-portlet__head-text title_sub pt-4">
                            {{-- <i class="la la-group"></i> &nbsp; --}}
                            List Pallet
                        </h4>
                        <p class="sub">
                            Berikut ini adalah list pallet yang terdapat pada <span class="text-ungu kt-font-bolder">{{$nama_gudang}}.</span>
                        </p>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group pt-4">
                            <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Nama Material</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tipe</th>
                                <th scope="col">Jenis</th>
                                <th scope="col" width="30%;">Alasan</th>
                                <th scope="col">Foto</th>
                            </tr>
                        </thead>
                    </table>					
                </div>
            </div>
            <!--End::Dashboard 6-->
        </div>
    </div>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                            <input type="text" class="form-control" readonly placeholder="Pilih tanggal" name="tanggal" id="tanggal" readonly value="{{date('d-m-Y')}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pallet</label>
                                <select class="form-control input-enter m-select2" readonly placeholder="Pilih tanggal" name="material" id="material" style="width: 100%"/>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Foto</label>
                                <div class="kt-dropzone dropzone" id="m-dropzone-one" >
                                    <div class="kt-dropzone__msg dz-message needsclick">
                                        <h3 class="kt-dropzone__msg-title">Seret berkas atau klik untuk mengunggah</h3>
                                        <span class="kt-dropzone__msg-desc">Hanya berkas dengan format <strong>jpg, png, jpeg, gif</strong> yang diizinkan untuk diunggah</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Shift</label>
                                <select class="form-control" name="shift_id" id="shift_id" readonly placeholder="Pilih shift">
                                    @foreach ($shift as $item)
                                        <option value="{{$item->id}}">{{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jumlah (pcs)</label>
                                <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Masukkan jumlah">
                            </div>
                            <div class="form-group">
                                <label>Tipe</label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="mengurangi" value="1" checked="checked" name="tipe"> Mengurangi 
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="menambah" value="2" name="tipe"> Menambah
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Jenis</label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="stok" value="1" checked="checked" name="jenis"> Stok
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="dipakai" value="2" name="jenis"> Dipakai
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="kosong" value="3" name="jenis"> Kosong
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" id="rusak" value="4" name="jenis"> Rusak
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Alasan</label>
                                <textarea class="form-control" name="alasan" id="alasan" rows="3" placeholder="Masukkan alasan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in"  id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--begin::Modal-->
<div class="modal fade btn_close_modal" id="modal_pallet" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">List Pallet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" id="list">
                            </div>
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

<script>
    // $('#tanggal').datepicker({
    //     rtl: KTUtil.isRTL(),
    //     todayHighlight: true,
    //     format:'dd-mm-yyyy',
    //     orientation: "bottom left",
    //     clearBtn:true,
    // });
    
    const id_gudang = "{{ $id_gudang }}";

    function showPallet(status) {
        $("#modal_pallet").modal("show");
        $("#list").html("");
        $.ajax({
            url: "{{url('list-pallet/pallets')}}/"+id_gudang+"/"+status,
            beforeSend:()=>{
                $("#list").html("<strong>Memuat Data</strong>")
            },
            success:response=>{
                let html = '<table class="table">';
                if (response.data == "") {
                    $("#list").html("<strong>Tidak ada pallet</strong>");
                } else {
                    html += "<thead><tr><th>#</th><th>Nama Pallet</th><th>Jumlah Pallet</th></tr></thead>";

                    const data = response.data;
                    html += "<tbody>";
                    let no=1;
                    data.forEach(element => {
                        html += "<tr>";
                        html += "<td>"+no+"</td>";
                        html += "<td>"+element.material.nama+"</td>";
                        html += "<td>"+element.jumlah+"</td>";
                        html += "</tr>";
                        no++;
                    });

                    html += "</tbody></table>";

                    $("#list").html(html);
                }

            },
            error:err=>{

            }
        });
    }
    
</script>
<script src="{{asset('assets/extends/js/page/list-pallet.js')}}" type="text/javascript"></script>
@endsection
