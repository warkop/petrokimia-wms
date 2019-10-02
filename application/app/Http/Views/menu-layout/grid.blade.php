@extends('layout.app')

@section('title', 'Layout')

@section('content')

<link rel="stylesheet" href="{{asset('assets/extends/css/map.css')}}">

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    {{-- <i class="la la-group"></i> &nbsp; --}}
                    Layout
                </h4>
                <p class="sub">
                    Berikut ini adalah layout yang terdapat pada <span class="text-ungu kt-font-bolder">Aplikasi WMS
                        Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                    {{-- <a href="#" class="btn btn-success btn-elevate btn-elevate-air">
                        <i class="la la-plus"></i> 
                        Edit Layout
                    </a> --}}
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-md-9">
                    <div id="kt_gmap_1" style="height:500px;"></div>
                </div>
                <div class="col-md-3">
                    <h4 class="col-12">Info Detail Area</h4>
                    <p class="col-12 mb2">Kapasitas Area : <strong>400 Ton</strong></p>
                    <div class="border-pembatas mb1"></div>
                    <div class="col-12 mb1">
                        <p class="boldd-500">
                            Tanggal : 30 September 2019
                        </p>
                        <p class="boldd-500">
                            Urea 100 Ton
                        </p>
                        <div class="border-pembatas"></div>
                    </div>
                    <div class="col-12">                        
                        <p class="boldd-500">
                            Tanggal : 1 Oktober 2019
                        </p>
                        <p class="boldd-500">
                            ZA 200 Ton
                        </p>
                        <div class="border-pembatas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->

<script src="{{asset('assets/extends/js/page/maps.js')}}"></script>

@endsection