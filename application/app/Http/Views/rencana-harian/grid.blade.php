@extends('layout.app')

@section('title', 'Rencana Harian')

@section('content')

<script>
    document.getElementById('rencanaHarian-nav').classList.add('kt-menu__item--active');
</script>



<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Rencana Harian
                </h4>
                <p class="sub">
                    Berikut ini adalah rencana harian <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="{{url('/rencana-harian/tambah')}}" class="btn btn-wms btn-elevate btn-elevate-air"><i class="la la-plus"></i> Tambah Data</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Shift Kerja</th>
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
<div class="modal fade" id="kt_modal_1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama gudang" disabled
                                    value="12/09/2019">
                            </div>
                            <div class="form-group">
                                <label>Shift</label>
                                <select class="form-control" id="exampleSelect1">
                                    <option>Shift 1</option>
                                    <option>Shift 2</option>
                                    <option>Shift 3</option>
                                    <option>Shift 4</option>
                                    <option>Shift 5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Admin Gudang</label>
                                <select class="form-control m-select2" id="kt_admin_gudang" name="param">
                                    <option value="" selected disabled></option>
                                    <option value="Asmianto Rahayu">Asmianto Rahayu</option>
                                    <option value="Asmianto Rahayu">Asmianto Rahayu</option>
                                    <option value="Cahyo Prasetyo">Cahyo Prasetyo</option>
                                    <option value="Galar Rahimah">Galar Rahimah</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Loket</label>
                                <select class="form-control m-select2" id="kt_loket" name="param">
                                    <option value="" selected disabled>
                                    </option>
                                    <option value="lk1">Loket 1</option>
                                    <option value="lk2">Loket 2</option>
                                    <option value="lk3">Loket 3</option>
                                    <option value="lk4">Loket 4</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Operator</label>
                                <select class="form-control m-select2" id="kt_operator" name="param">
                                    <option value="" selected disabled>
                                    </option>
                                    <option value="Dinda Astuti">Dinda Astuti</option>
                                    <option value="Sarah Namaga">Sarah Namaga</option>
                                    <option value="Ibun Winarsih">Ibun Winarsih</option>
                                    <option value="Jumari Kurniawan">Jumari Kurniawan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>House Keeping</label>
                                <select class="form-control m-select2" id="kt_housekeeping" name="param">
                                    <option value="" selected disabled></option>
                                    <option value="Eka Farida">Eka Farida</option>
                                    <option value="Jaeman Sitompul">Jaeman Sitompul</option>
                                    <option value="Raden Prayoga">Raden Prayoga</option>
                                    <option value="Puti Prakasa">Puti Prakasa</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Checker</label>
                                <select class="form-control m-select2" id="kt_checker" name="param">
                                    <option value="" selected disabled></option>
                                    <option value="Unggul Mustofa">Unggul Mustofa</option>
                                    <option value="Ganjaran Yolanda">Ganjaran Yolanda</option>
                                    <option value="Rahmi Nashiruddin">Rahmi Nashiruddin</option>
                                    <option value="Sari Dabukke">Sari Dabukke</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->






<script src="{{asset('assets/extends/js/page/rencana-harian.js')}}" type="text/javascript"></script>
@endsection