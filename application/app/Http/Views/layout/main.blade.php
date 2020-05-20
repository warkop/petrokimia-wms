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
                                <img src="{{asset('assets/extends/img/logo/logo_wms1.png')}}" alt="{{asset('assets/extends/img/no-image.png')}}" width="100%">
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
                            @if (auth()->user()->can('dashboard'))
                            <div class="col-6 mb2">
                                <button onclick="location.href='{{url('/dashboard')}}'" type="button"
                                    class="btn btn-elevate btn-outline-success btn-icon-sm" style="width: 100%;"> <em class="la la-desktop"></em>
                                    Dashboard</button>
                            </div>
                            @endif
                            @if (auth()->user()->can('data-master'))
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%;"> <em class="la la-archive"></em> Masters </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item">
                                            <a href="{{('master-aktivitas')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-line-chart"></em>
                                                <span class="kt-nav__link-text">Aktivitas</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-karu')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-user"></em>
                                                <span class="kt-nav__link-text">Kepala Regu</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-material')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-clone"></em>
                                                <span class="kt-nav__link-text">Material</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-tenaga-kerja-nonorganik')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-user-times"></em>
                                                <span class="kt-nav__link-text">Tenaga Kerja Non Organik</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-pemetaan-sloc')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-building"></em>
                                                <span class="kt-nav__link-text">Pemetaan Sloc</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-kerusakan-alat')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-wrench"></em>
                                                <span class="kt-nav__link-text">Kerusakan Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-kategori-alat-berat')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-truck"></em>
                                                <span class="kt-nav__link-text">Kategori Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-jenis-foto')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-photo"></em>
                                                <span class="kt-nav__link-text">Jenis Foto</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('master-yayasan')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-hand-lizard-o"></em>
                                                <span class="kt-nav__link-text">Yayasan</span>
                                            </a>
                                        </li>
                                        @if (auth()->user()->can('data-master-user'))
                                            <li class="kt-nav__item">
                                                <a href="{{('master-user')}}" class="kt-nav__link">
                                                    <em class="kt-nav__link-icon la la-users"></em>
                                                    <span class="kt-nav__link-text">User</span>
                                                </a>
                                            </li>    
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endif
                            @if (auth()->user()->can('main-menu'))
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <em class="la la-laptop"></em> Main Menu</button>
                                
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        @if (auth()->user()->can('layout'))
                                        <li class="kt-nav__item">
                                            <a href="{{('layout')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-map"></em>
                                                <span class="kt-nav__link-text">Layout</span>
                                            </a>
                                        </li>
                                        @endif
                                        @if (auth()->user()->can('gudang'))
                                        <li class="kt-nav__item">
                                            <a href="{{('gudang')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-institution"></em>
                                                <span class="kt-nav__link-text">Gudang</span>
                                            </a>
                                        </li>
                                        @endif
                                        @can ('view', App\Http\Models\RencanaHarian::class)
                                        {{-- <li class="kt-nav__item">
                                            <a href="{{('rencana-harian')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la la-calendar"></em>
                                                <span class="kt-nav__link-text">Rencana Harian</span>
                                            </a>
                                        </li> --}}
                                        @endcan
                                        @if (auth()->user()->can('penerimaan-gp'))
                                        <li class="kt-nav__item">
                                            <a href="{{('penerimaan-gp')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-columns"></em>
                                                <span class="kt-nav__link-text">Penerimaan GP</span>
                                            </a>
                                        </li>
                                        @endif
                                        @if (auth()->user()->can('log-aktivitas'))
                                        <li class="kt-nav__item">
                                            <a href="{{('log-aktivitas')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-line-chart"></em>
                                                <span class="kt-nav__link-text">Log Aktivitas</span>
                                            </a>
                                        </li>
                                        @endif
                                        @if (auth()->user()->can('log-aktivitas-user'))
                                        <li class="kt-nav__item">
                                            <a href="{{('/log-aktivitas-user')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-motorcycle"></em>
                                                <span class="kt-nav__link-text">Log Aktivitas User</span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endif
                            @if (auth()->user()->can('report'))
                            <div class="col-6 mb2">
                                <button type="button" class="btn btn-outline-success btn-icon-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%"> <em class="la la-file"></em> Report</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__item">
                                            <a href="{{url('report/laporan-transaksi-material')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-table"></em>
                                                <span class="kt-nav__link-text">Transaksi Material</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{url('report/laporan-stok')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-th-large"></em>
                                                <span class="kt-nav__link-text">Posisi Stok</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-mutasi-pallet')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-arrows-alt-h"></em>
                                                <span class="kt-nav__link-text">Mutasi Pallet</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-mutasi-stok')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-arrows-alt-v"></em>
                                                <span class="kt-nav__link-text">Mutasi Stok</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-produk')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-boxes"></em>
                                                <span class="kt-nav__link-text">Produk</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-material')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-warehouse"></em>
                                                <span class="kt-nav__link-text">Material</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-realisasi')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-check-square"></em>
                                                <span class="kt-nav__link-text">Realisasi</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{url('/report/laporan-keluhan-alat-berat')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon fa fa-truck-moving"></em>
                                                <span class="kt-nav__link-text">Keluhan Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-keluhan-gp')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-file-archive-o"></em>
                                                <span class="kt-nav__link-text">Keluhan GP</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-aktivitas')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-bicycle"></em>
                                                <span class="kt-nav__link-text">Aktivitas</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-log-sheet')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-empire"></em>
                                                <span class="kt-nav__link-text">Log Sheet</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-biaya-alat-berat')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-dollar"></em>
                                                <span class="kt-nav__link-text">Biaya Alat Berat</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-biaya-tkbm')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-euro"></em>
                                                <span class="kt-nav__link-text">Biaya TKBM</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-biaya-pallet')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-bitcoin"></em>
                                                <span class="kt-nav__link-text">Biaya Pallet</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="{{('report/laporan-keluhan-operator')}}" class="kt-nav__link">
                                                <em class="kt-nav__link-icon la la-bitcoin"></em>
                                                <span class="kt-nav__link-text">Keluhan Operator</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endif
                            <div class="col-6">
                                <button onclick="location.href='{{url('/logout')}}'" type="button" class="btn btn-outline-danger btn-icon-sm" style="width: 100%;"> <em class="la la-times-circle-o"></em> Keluar</button>
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