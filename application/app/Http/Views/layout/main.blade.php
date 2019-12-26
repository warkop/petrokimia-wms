<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->

<head>
    <meta charset="utf-8" />
    <title>WMS | Warehouse Management System</title>
    <meta name="description" content="Login page example">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('layout.header')
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{aset_extends('css/global.css')}}">
    <link href="{{asset('assets/extends/login-v5.default.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/extends/perfect-scrollbar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{asset('assets/media/logos/favicon.ico')}}" />
    <style>
    #body {
        font-family: 'Poppins', sans-serif;
    }
    </style>
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body
    class="kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

    <!-- begin:: Page -->
    <div id="body" class="kt-grid kt-grid--ver kt-grid--root">
        <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v5 kt-login--signin">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile"
                style="background-image: url('{{asset('assets/extends/bg-3.jpg')}}');">
                <div class="kt-login__left">
                    <div class="kt-login__wrapper">
                        <div class="kt-login__content">
                            <a class="kt-login__logo" href="{{('master-aktivitas')}}">
                                <img src="{{asset('assets/extends/img/logo/logo_wms1.png')}}" width="100%">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="kt-login__right">
                    <div class="kt-login__wrapper" style="padding: none !important;">
                        <div class="kt-login__signin mb2">
                            <div class="kt-login__head">
                                <h1 class="text-left" style="transform:translateY(-5vh)">Welcome to Warehouse Management
                                    System</h1>
                            </div>
                            <p class="boldd-500">Aplikasi WMS adalah aplikasi yang dimiliki oleh Petrokimia Gresik yang
                                digunakan untuk
                                monitoring aktivitas gudang.</p>
                        </div>
                        <div class="row">
                            <div class="col-6 mb2">
                                <button onclick="location.href='{{url('/')}}'" type="button"
                                    class="btn btn-elevate btn-outline-success btn-icon-sm" style="width: 100%;"> <i class="la la-desktop"></i>
                                    Dashboard</button>
                            </div>
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%;"> <i
                                        class="la la-archive"></i> Masters </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        {{-- <li class="kt-nav__section kt-nav__section--first">
                                                <span class="kt-nav__section-text">Choose an
                                                    option</span>
                                            </li> --}}
                                        <li class="kt-nav__item">
                                            <a href="{{('master-aktivitas')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-line-chart"></i>
                                                <span class="kt-nav__link-text">Aktivitas</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-karu')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-user"></i>
                                                <span class="kt-nav__link-text">Kepala Regu</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-material')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-clone"></i>
                                                <span class="kt-nav__link-text">Material</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-tenaga-kerja-nonorganik')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-user-times"></i>
                                                <span class="kt-nav__link-text">Tenaga Kerja Non Organik</span>
                                            </a>
                                        </li>
                                        {{-- <li class="kt-nav__item">
                                            <a href="{{('master-pekerjaan')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-suitcase"></i>
                                                <span class="kt-nav__link-text">Job Desk</span>
                                            </a>
                                        </li> --}}
                                        <li class="kt-nav__item">
                                            <a href="{{('master-pemetaan-sloc')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-building"></i>
                                                <span class="kt-nav__link-text">Pemetaan Sloc</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-kerusakan-alat')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-wrench"></i>
                                                <span class="kt-nav__link-text">Kerusakan Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-kategori-alat-berat')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-truck"></i>
                                                <span class="kt-nav__link-text">Kategori Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-jenis-foto')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-photo"></i>
                                                <span class="kt-nav__link-text">Jenis Foto</span>
                                            </a>
                                        </li>
                                        @if (session('userdata')['role_id'] == 1)
                                            <li class="kt-nav__item">
                                                <a href="{{('master-user')}}" class="kt-nav__link">
                                                    <i class="kt-nav__link-icon la la-users"></i>
                                                    <span class="kt-nav__link-text">User</span>
                                                </a>
                                            </li>    
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i
                                        class="la la-laptop"></i> Main Menu</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item">
                                            <a href="{{('layout')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-map"></i>
                                                <span class="kt-nav__link-text">Layout</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('gudang')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-institution"></i>
                                                <span class="kt-nav__link-text">Gudang</span>
                                            </a>
                                        </li>
                                        @if (session('userdata')['role_id'] == 5)
                                        <li class="kt-nav__item">
                                            <a href="{{('rencana-harian')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la la-calendar"></i>
                                                <span class="kt-nav__link-text">Rencana Harian</span>
                                            </a>
                                        </li>
                                        @endif
                                        <li class="kt-nav__item">
                                            <a href="{{('penerimaan-gp')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-columns"></i>
                                                <span class="kt-nav__link-text">Penerimaan GP</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('log-aktivitas')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-line-chart"></i>
                                                <span class="kt-nav__link-text">Log Aktivitas</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%"> <i
                                        class="la la-file"></i> Report</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-material')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-table"></i>
                                                <span class="kt-nav__link-text">Material</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-stok')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-th-large"></i>
                                                <span class="kt-nav__link-text">Stok</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-absen-karyawan')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la fa fa-fingerprint"></i>
                                                <span class="kt-nav__link-text">Absen Karyawan</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-mutasi-pallet')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon fa fa-arrows-alt-h"></i>
                                                <span class="kt-nav__link-text">Mutasi Pallet</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-mutasi-stok')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon fa fa-arrows-alt-v"></i>
                                                <span class="kt-nav__link-text">Mutasi Stok</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-produk')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon fa fa-boxes"></i>
                                                <span class="kt-nav__link-text">Produk</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-realisasi')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon fa fa-check-square"></i>
                                                <span class="kt-nav__link-text">Realisasi</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{url('/report/laporan-keluhan-alat-berat')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon fa fa-truck-moving"></i>
                                                <span class="kt-nav__link-text">Keluhan Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-keluhan-gp')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-archive-o"></i>
                                                <span class="kt-nav__link-text">Keluhan GP</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('laporan-aktivitas')}}" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-archive-o"></i>
                                                <span class="kt-nav__link-text">Aktivitas</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6">
                                <button onclick="location.href='{{url('/logout')}}'" type="button" class="btn btn-outline-danger btn-icon-sm" style="width: 100%;"> <i class="la la-times-circle-o"></i> Keluar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layout.footer')
</body>

<!-- end::Body -->

</html>