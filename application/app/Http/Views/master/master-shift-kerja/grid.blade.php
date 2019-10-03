@extends('layout.app')

@section('title', 'Master Shift Kerja')

@section('content')


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Master Shift Kerja
                </h4>
                <p class="sub">
                    Berikut ini adalah data master shift kerja yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
					<a href="#" class="btn btn-success btn-elevate btn-elevate-air" onclick="tambah()" data-toggle="modal"><i class="la la-plus"></i> Tambah Data</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>No</th>
                        <th>Nama Shift</th>
                        <th>Mulai Shift</th>
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
                    @if ($errors->count() > 0)
                        <div id="error_message" class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                    <input type="hidden" class="form-control" id="shift_kerja_id" name="shift_kerja_id">
                    <input type="hidden" name="action" id="action" value="add">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Shift</label>
                                <input type="text" class="form-control input-enter" name="nama_shift" id="nama_shift" placeholder="Masukkan nama shift">
                            </div>
                            <div class="form-group">
                                <label>Jam Mulai</label>
                                <div class="input-group timepicker">
                                    <input class="form-control input-enter" name="mulai_shift" id="kt_timepicker_2" readonly placeholder="Pilih jam" type="text" />
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-time"></i>
                                    </span>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control input-enter" name="start_date" id="start_date" readonly placeholder="Pilih Tanggal">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control input-enter" name="end_date" id="end_date" readonly placeholder="Pilih Tanggal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success ladda-button" data-style="zoom-in"  id="btn_save">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->



{{-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" /> --}}

<script src="{{asset('assets/extends/js/page/master-shift-kerja.js')}}" type="text/javascript"></script>
<script>
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    orientation: "bottom left"
});

$("#kt_timepicker_2").timepicker({
    minuteStep: 1,
    defaultTime: 'current',
    showSeconds: false,
    showMeridian: false,
    snapToStep: true,
    icons: {
        up: 'fa fa-angle-up',
        down: 'fa fa-angle-down'
    }
});

// $('#kt_timepicker_2').timepicker({
//     uiLibrary: 'bootstrap4'
// });
</script>
@endsection
