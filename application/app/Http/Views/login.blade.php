<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">    
    {{-- <title>Login Aplikasi WMS</title> --}}
    <title>
        {{ (empty($title)? '' : $title.' | ').app_info(['title'])}}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{app_info('description')}}">
    <meta name="author" content="{{app_info(['vendor', 'company'])}}">
	<link rel="shortcut icon" href="{{ aset_extends('img/logo/favwms.png')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        var baseUrl = "{{url('/')}}/";
    </script>

    <link rel="stylesheet" href="{{aset_extends()}}login/css/bootstrap.css">
    <link rel="stylesheet" href="{{aset_extends()}}login/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="{{aset_extends()}}css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{aset_extends()}}login/css/main-login-wms.css" />
    <link href="{{aset_tema('vendors/general/sweetalert2/dist/sweetalert2.css')}}" rel="stylesheet" type="text/css" />
    
    <script src="{{ aset_extends()}}login/js/jquery.js"></script>
    <script src="{{aset_extends()}}login/js/bootstrap.js"></script>
    <script src="{{ aset_extends()}}login/js/bootstrap.min.js"></script>
    <script src="{{ aset_extends()}}login/js/jquery.sticky.js"></script>
    <link rel="stylesheet" href="{{aset_extends('plugin/ladda/dist/ladda-themeless.min.css')}}">
    <script src="{{aset_extends('plugin/ladda/dist/spin.min.js')}}"></script>
    <script src="{{aset_extends('plugin/ladda/dist/ladda.min.js')}}"></script>
    <script src="{{aset_tema('vendors/general/sweetalert2/dist/sweetalert2.min.js')}}" type="text/javascript"></script>
    <script src="{{aset_tema('vendors/custom/components/vendors/sweetalert2/init.js')}}" type="text/javascript"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-102442286-15"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-102442286-15');
    </script>
</head>
<body>
    <div class="body">
        <div class="login">
            <div class="row">
                <div id="lefty" class="col-lg-5 left ">
                    <img id="img-lefty" class="logo" src="{{ aset_extends('img/logo/logo_petro1.png')}}" alt="" srcset="">            
                    <img class="full" src="{{ aset_extends('img/illustration/login-wms.png')}}" alt="" srcset="">            
                </div>
                
                <div id="rightty" class="col-lg-7 right" style="background: white;z-index: 99;">
                    <div class="login-wrapper">
                        <div class="top-logo">
                            <span>
                                    <img src="{{ aset_extends('img/logo/logo_ggmu1.png')}}" alt="Gudang Gresik Makin Unggul">
                                    <img src="{{ aset_extends('img/logo/logo_wms1.png')}}" alt="Warehouse Management System">
                                    <img src="{{ aset_extends('img/logo/logo_petro1.png')}}" alt="PT Petrokimia Gresik">
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
                            <form class="kt-form center" action="" id="form1" method="post" onsubmit="return false">
                                <input type="hidden" name="last_url" id="last_url" value="{{$source}}">
                                <div class="username form-group">
                                    <label for="">Username</label>
                                    <input type="text" class="form-control input-login" id="username" name="username" placeholder="masukkan username" autocomplete="username">
                                </div>
                                <div class="password form-group">
                                    <label for="">Password</label>
                                    <input type="password" class="form-control input-login" id="password" name="password" placeholder="masukkan password" autocomplete="current-password">
                                </div>
                                <div class="button">
                                    <button type="button" class="btn btn-pills btn-block btn-brand-cta btn-lg float-right btn-shadow-login-invert ladda-button" data-style="zoom-in" id="btn_login">
                                        Sign In
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div>
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
    <script>
        window.onresize = function() {
            toggle();
        }

        $(function() {
            toggle();
        });

        function toggle() {
            if (window.innerWidth < 800) {
                $('#img-lefty').hide();
                // document.getElementById('img-lefty').style.display = 'none';      
            }
            else {
                $('#img-lefty').show();         
            }    
        }
        var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"dark": "#282a3c",
						"light": "#ffffff",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995"
					},
					"base": {
						"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
						"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
					}
				}
			};

        @php
        $alerts = session('alerts');

        if(!empty($alerts)){
            foreach ($alerts as $key => $value) {
                @endphp
                swal.fire("<?php echo $value[2]; ?>", "<?php echo $value[1]; ?>", "<?php echo $value[0]; ?>");
                @php
            }
        }
        @endphp

        var username, password, laddaLogin;

        $(document).ready(function(){
            $('#btn_login').click(function(e){
                username = $("#username").val();
                password = $("#password").val();

                if(username == "" || password == ""){
                    swal.fire("Oopss...","Form login tidak boleh kosong!","error");
                }else{
                    e.preventDefault();
                    laddaLogin = Ladda.create(this);
                    laddaLogin.start();

                    setTimeout(function(){
                        login();
                    },1200);
                }
            });

            $('.input-login').on("keyup", function(event) {
                event.preventDefault();
                if (event.keyCode === 13) {
                    $("#btn_login").click();
                }
            });
        });


        function login() {
            $.ajax({
                type : "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {username:username,password:password},
                url : "authenticate",
                success : function(response){

                    if(response.code == 200){
                        laddaLogin.stop();
                        // swal.fire("Hello","Selamat Datang "+response.data.fullname,"success");
                        var last_url = $('#last_url').val();

                        last_url = (last_url == '')? baseUrl : last_url;
                        
                        // setTimeout(function(){
                            document.location = last_url;
                        // }, 1000);
                    }else{
                        laddaLogin.stop();
                        swal.fire("Pemberitahuan!",response.message,"warning");
                    }
                },
                error : function(response){
                    var head = 'Maaf', message = 'Terjadi kesalahan koneksi', type = 'error';
                    window.onbeforeunload = false;
                    laddaLogin.stop();

                    if(response['status'] == 419){
                        location.reload();
                    }else{
                        if(response['status'] != 404 && response['status'] != 500 ){
                            var obj = JSON.parse(response['responseText']);

                            if(!$.isEmptyObject(obj.message)){
                                if(obj.code == 401){
                                    head = 'Pemberitahuan';
                                    message = obj.message;
                                    type = 'warning';
                                }else if(obj.code > 401){
                                    head = 'Maaf';
                                    message = obj.message;
                                    type = 'error';
                                }else{
                                    head = 'Pemberitahuan';
                                    message = obj.message;
                                    type = 'warning';
                                }
                            }
                        }

                        swal.fire(head, message, type);
                    }
                }
            });
        }
    </script>
</body>
</html>