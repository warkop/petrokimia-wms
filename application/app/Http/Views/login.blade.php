<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">    
    <title>Login Aplikasi WMS</title>
	<link rel="shortcut icon" href="{{ asset('/assets/extends/img/logo/fav_wms@2x.png')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link rel="stylesheet" href="assets/extends/login/css/bootstrap.css">
    <link rel="stylesheet" href="assets/extends/login/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="assets/extends/css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="assets/extends/login/css/main-login-wms.css" />

<script src="{{ url('/')}}/assets/extends/login/js/bootstrap.js"></script>
    <script src="assets/extends/login/js/bootstrap.min.js"></script>
    <script src="assets/extends/login/js/jquery.js"></script>
    <script src="assets/extends/login/js/jquery.sticky.js"></script>
    <!-- <script src="main.js"></script> -->
</head>
<body>
    <div class="body">
        <div class="login">
            <div class="row">
                <div class="col-lg-5 left">
                    
                    <img class="logo" src="{{ asset('assets/extends/img/logo/Logo_petro.png')}}" alt="" srcset="">            
                    <img class="full" src="{{ asset('assets/extends/img/illustration/login-wms.png')}}" alt="" srcset="">            
                    
                </div>
                
                <div class="col-lg-7 right">
                    <div class="login-wrapper">
                        <div class="top-logo">
                            <span>
                                    <img src="{{ asset('assets/extends/img/logo/logo-wms.png')}}" alt="PT Petrokimia Gresik">
                            </span>
                            <!-- <span> <p>Dinas Kebudayaan dan Pariwisata Kota Surabaya </p></span> -->
                        </div>
                        <div class="form-login">
                            <p class="title">
                                Warehouse Management System
                            </p>
                            <p class="sub-title">
                                Aplikasi manajemen kinerja gudang dan bagian Manajemen Kualitas 
                                pemuatan produk pupuk/non pupuk
                            </p>
                            <form class="center">
                                    <div class="username form-group">
                                            <label for="">Username</label>
                                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="masukkan username">
                                          </div>
                                    <div class="password form-group">
                                      <label for="">Password</label>
                                      <input type="password" class="form-control" id="exampleFormControlInput1" placeholder="masukkan password">
                                    </div>
                                    <div class="button">
                                            <a href="{{url('/master-user')}}" class="btn btn-pills btn-block btn-brand-cta btn-lg float-right btn-shadow-login-invert">
                                                Sign In
                                            </a>
                                    </div>
                            </form>

                        </div>
                        <div>
                            <!-- <img class="foot-illus" src="assets/extends/img/illustration/foot-illus.png" alt="" srcset="">             -->
                        </div>
                    </div>
                    <div class="footer-bottom">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <center>
                                        <p>©2019 &middot;<a href="#"> PT Petrokimia Gresik</a> &middot; All rights
                                            reserved</p>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
           
        </div>
    </div>
</body>
</html>