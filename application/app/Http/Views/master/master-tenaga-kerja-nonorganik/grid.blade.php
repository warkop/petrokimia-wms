@extends('layout.app')

@section('title', 'Master Tenaga Kerja Non Organik')

@section('content')
<script>
    document.getElementById('master-tenagaNO-nav').classList.add('kt-menu__item--active');
</script>


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Tenaga Kerja Non Organik
                </h4>
                <p class="sub">
                    Berikut ini adalah data master tenaga kerja non-organik yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
                        <a href="#" class="btn btn-orens btn-elevate btn-elevate-air" data-toggle="modal" data-target="#kt_modal_2"><i class="la la-plus"></i> Set Anggaran</a>
					<a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>No</th>
                        <th>Nama</th>
                        <th>No. Hp</th>
                        <th>Job Desk</th>
                        <th>Start Date</th>
                        <th>End Date</th>
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
<div class="modal fade btn_close_modal" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form  id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Tenaga Kerja</label>
                                <input type="text" class="form-control input-enter" id="nama" name="nama" placeholder="Masukkan nama tenaga kerja">
                            </div>
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control input-enter" id="nik" name="nik" placeholder="Masukkan NIK" maxlength="20">
                            </div>
                            <div class="form-group">
                                <label>Nomor Hp</label>
                                <input type="text" class="form-control input-enter" id="nomor_hp" name="nomor_hp" placeholder="Ex. 0895340952989">
                            </div>
                            <div class="form-group">
                                <label>Job Desk</label>
                                <select class="form-control m-select2" id="job_desk_id" name="job_desk_id" aria-placeholder="Pilih kategori" style="width: 100%;">
                                    <option value="">Pilih pekerjaan</option>
                                    @foreach ($job_desk as $item)
                                        <option value="{{$item->id}}">{{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nomor BPJS</label>
                                <input type="text" class="form-control input-enter" id="nomor_bpjs" name="nomor_bpjs" placeholder="Masukkan Nomor BPJS">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control input-enter" id="start_date" name="start_date" readonly placeholder="Select date">
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
<div class="modal fade" id="kt_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Anggaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nominal Anggaran</label>
                                <input type="text" class="form-control" placeholder="Masukkan nominal anggaran">
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





<script src="{{asset('assets/extends/js/page/master-tenaga-kerja.js')}}" type="text/javascript"></script>
<script>
$('#job_desk_id').select2({
    placeholder: "Select Job Desk"
});
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    orientation: "top left"
});
</script>
@endsection
