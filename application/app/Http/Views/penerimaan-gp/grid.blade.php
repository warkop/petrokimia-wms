@extends('layout.app')

@section('title', 'Aktivitas')

@section('content')

<script>
    document.getElementById('pGP-nav').classList.add('kt-menu__item--active');
</script>


<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Penerimaan Gp
                </h4>
                <p class="sub">
                    Berikut ini adalah Penerimaan Gp <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            {{-- <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    <a href="#" class="btn btn-wms btn-elevate btn-elevate-air" data-toggle="modal"
                        data-target="#kt_modal_1"><i class="la la-plus"></i> Tambah Aktivitas</a>
                </div>
            </div> --}}
        </div>
        <div class="kt-portlet__body">
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Aktivitas</th>
                        <th scope="col">Status</th>
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
<div class="modal fade" id="kt_modal_1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Aktivitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-20 pointer hov-none" onclick="window.location='{{url('aktivitas/tambah')}}';">
                            <div class="card bg-success text-white text-center p-3">
                                <blockquote class="blockquote mb-0">
                                    <p>Aktivitas Pindah Area</p>
                                    <h1 style="font-size:50px">A</h1>
                                    <footer class="blockquote-footer text-white">
                                        <small>
                                            Tambah data terkait aktivitas area
                                        </small>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                        <div class="col-20 pointer hov-none" onclick="window.location='{{url('aktivitas/tambah')}}';">
                            <div class="card bg-success text-white text-center p-3">
                                <blockquote class="blockquote mb-0">
                                    <p>Aktivitas Produksi</p>
                                    <h1 style="font-size:50px">P</h1>
                                    <footer class="blockquote-footer text-white">
                                        <small>
                                            Tambah data terkait aktivitas produksi
                                        </small>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                        <div class="col-20 pointer hov-none" onclick="window.location='{{url('aktivitas/tambah')}}';">
                            <div class="card bg-success text-white text-center p-3">
                                <blockquote class="blockquote mb-0">
                                    <p>Aktivitas Kirim ke GP</p>
                                    <h1 style="font-size:50px">K</h1>
                                    <footer class="blockquote-footer text-white">
                                        <small>
                                            Tambah data terkait aktivitas kirim ke GP
                                        </small>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                        <div class="col-20 pointer hov-none" onclick="window.location='{{url('aktivitas/tambah')}}';" >
                            <div class="card bg-success text-white text-center p-3">
                                <blockquote class="blockquote mb-0">
                                    <p>Aktivitas Kirim ke Yayasan</p>
                                    <h1 style="font-size:50px">K</h1>
                                    <footer class="blockquote-footer text-white">
                                        <small>
                                            Tambah data terkait aktivitas Yayasan
                                        </small>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                        <div class="col-20 pointer hov-none" onclick="window.location='{{url('aktivitas/tambah')}}';">
                            <div class="card bg-success text-white text-center p-3">
                                <blockquote class="blockquote mb-0">
                                    <p>Aktivitas Terima Produk</p>
                                    <h1 style="font-size:50px">T</h1>
                                    <footer class="blockquote-footer text-white">
                                        <small>
                                            Tambah data terkait aktivitas terima produk
                                        </small>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-clean" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->






<script src="{{asset('assets/extends/js/page/penerimaan-gp.js')}}" type="text/javascript"></script>
@stop