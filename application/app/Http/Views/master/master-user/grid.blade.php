@extends('layout.app')

@section('title', 'Master User')

@section('content')
<script>
    document.getElementById('master-user-nav').classList.add('kt-menu__item--active');
</script>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Data Master User
                </h4>
                <p class="sub">
                    Berikut ini adalah data master user yang tercatat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
					<a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal" onclick="tambah()"><i class="la la-plus"></i> Tambah Data</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Username</th>
                        <th scope="col">Nama Pegawai</th>
                        <th scope="col">Hak Akses</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
						<th scope="col">Actions</th>
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
            <form  id="form1" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <input type="hidden" name="action" id="action" value="add">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control input-enter" name="nama" id="nama" placeholder="Masukkan nama">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control input-enter" name="email" id="email" placeholder="Masukkan email">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control input-enter" name="username" id="username" placeholder="Masukkan username">
                            </div>
                            <div class="form-group" id="tempat_password">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control input-enter pwd" name="password" id="password" placeholder="Password" aria-describedby="basic-addon2">
                                    <div class="input-group-append reveal" style="cursor: pointer;"><span class="input-group-text kt-font-dark" id="basic-addon2">Show</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Pilih Hak Akses</label><br>
                                @foreach ($role as $key)
                                <span class="mr-2">
                                    <input type="radio" id="radio{{$key->id}}" name="role_id" onchange="loadPegawai()" value="{{$key->id}}">
                                    <label for="radio{{$key->id}}">{{$key->nama}}</label>
                                </span>
                                @endforeach
                            </div>
                            <div class="form-group" id="pilih_pegawai">
                                <label id="label_pilih">Pilih Pegawai</label><br>
                                <select class="form-control" id="pilih" name="pilih" style="width: 100%;">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control input-enter" id="start_date" name="start_date" readonly placeholder="Pilih tanggal" value="{{date('d-m-Y')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control input-enter" id="end_date"  name="end_date" readonly placeholder="Pilih tanggal">
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

<div class="modal fade btn_close_modal" id="modal_form_ganti_password" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ganti Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="form2" class="kt-form" action="" method="post" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id_user" name="id_user">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" id="tempat_old_password">
                                <label>Password Lama</label>
                                <div class="input-group">
                                    <input type="password" class="form-control input-enter-change pwd" name="old_password" id="old_password" placeholder="Password Lama" aria-describedby="basic-addon2">
                                    <div class="input-group-append reveal" style="cursor: pointer;"><span class="input-group-text kt-font-dark" id="basic-addon2">Show</span></div>
                                </div>
                            </div>
                            <div class="form-group" id="tempat_password">
                                <label>Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control input-enter-change pwd" name="new_password" id="new_password" placeholder="Password Baru" aria-describedby="basic-addon2">
                                    <div class="input-group-append reveal" style="cursor: pointer;"><span class="input-group-text kt-font-dark" id="basic-addon2">Show</span></div>
                                </div>
                            </div>
                            <div class="form-group" id="tempat_password_confirmation">
                                <label>Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control input-enter-change pwd" name="new_password_confirmation" id="new_password_confirmation" placeholder="Konfirmasi Password" aria-describedby="basic-addon2">
                                    <div class="input-group-append reveal" style="cursor: pointer;"><span class="input-group-text kt-font-dark" id="basic-addon2">Show</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-wms ladda-button" data-style="zoom-in" id="btn_change">Simpan data</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="{{asset('assets/extends/js/page/master-user.js')}}" type="text/javascript"></script>
<script>
$('#end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format:'dd-mm-yyyy',
    clearBtn:true,
    orientation: "bottom left"
});

$('#pilih').select2({
    placeholder: "Silahkan pilih",
    dropdownParent:$("#pilih_pegawai")
});

$(".reveal").on('click',function() {
    var $pwd = $(".pwd");
    var $text = $("#basic-addon2");
    if ($pwd.attr('type') === 'password') {
        $pwd.attr('type', 'text');
        $text.html('Hide');
    } else {
        $pwd.attr('type', 'password');
        $text.html('Show');
    }
});
</script>
@endsection
