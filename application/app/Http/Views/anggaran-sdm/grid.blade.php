@extends('layout.app')

@section('title', 'Anggaran SDM')

@section('content')



<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Data Anggaran SDM
                </h4>
                <p class="sub">
                    Berikut ini adalah form digunakan untuk setting anggaran SDM <span class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-group pt-4">
                    
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="form-group">
                        <label>Jumlah</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon2" value="10">
                            <div class="input-group-append"><span class="input-group-text" id="basic-addon2">ton</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp.)</label>
                        <input type="text" class="form-control" placeholder="Username" value="100.000">
                    </div>
                    <button type="button" onclick="showme()" class="btn btn-success">Simpan data</button>
                </div>
            </div>
		</div>
	</div>
	<!--End::Dashboard 6-->
</div>
<!-- end:: Content -->



<script>
$('#start_date, #end_date').datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    orientation: "bottom left"
});
function showme() {
    swal.fire({
        title: 'Are you sure?',
        text: "Pengaturan anggaran SDM akan disimpan.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan data!'
    }).then(function (result) {
        if (result.value) {
            swal.fire(
                'Berhasil!',
                'Data berhasil dihapus.',
                'success'
            )
        }
    });
}
</script>
@endsection
