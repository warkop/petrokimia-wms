@extends('layout.app')

@section('title', 'Data Gudang')

@section('content')

<script>
    $('body').addClass("kt-aside--minimize");
    document.getElementById('gudang-nav').classList.add('kt-menu__item--active');
</script>
<link rel="stylesheet" href="{{asset('assets/extends/css/map.css')}}">
{{-- <script src="//maps.google.com/maps/api/js?key=AIzaSyBDHDV2ksjKZ8xtSOZEOBe4_DQM87VrXgI" type="text/javascript" defer></script> --}}
{{-- <script src="{{aset_tema()}}vendors/custom/gmaps/gmaps.js" type="text/javascript"></script> --}}
{{-- <script src="{{aset_tema()}}app/custom/general/components/maps/google-maps.js" type="text/javascript"></script> --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMHi0AIoQz1JmkicVxHhJJ7mf5cNeXucQ&libraries=drawing" async defer></script>
{{-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;libraries=drawing&amp;dummy=.js"></script> --}}
<!-- begin:: Content -->
<style>
    #layoutGudang {
        height: 90%;
        margin: 0px;
        padding: 0px
    }
</style>
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <!--Begin::Dashboard 6-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-title">
                <h4 class="kt-portlet__head-text title_sub pt-4">
                    Layout Gudang
                </h4>
                <p class="sub">
                    Berikut ini adalah data pemetaan layout gudang yang tercatat pada <span
                        class="text-ungu kt-font-bolder">Aplikasi WMS Petrokimia.</span>
                </p>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group pt-4">
                        <select class="form-control m-select2 col-12" style="width:200px" id="pilih_area" name="pilih_area">
                            <option value="" selected disabled>Pilih Area</option>
                        </select>
                    <a href="#" class="btn btn-success" data-toggle="modal"
                         onclick="muatArea()" style="min-width:130px">Muat Area</a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="col-md-12 text-center" style="min-height:50vh">
                <div id="layoutGudang" style="height:500px;display:none!important"></div>
                
                <div class="" id="noSelectWil">
                    <img class="text-center"  src="{{asset('assets/extends/img/illustration/wilayah.svg')}}" alt="" srcset="" style="width: 30vh;margin-top: 7vh;opacity: .8;"> <br>
                    <label id="labelnoSelectWil" class="boldd text-center" style="margin-top:3vh">Belum ada area yang dipilih</label>
                </div>
            </div>
        </div>
    </div>
    {{-- <div id="map"></div> --}}

    {{-- <form method="post" accept-charset="utf-8" id="map_form"> --}}
        <div class="row">
            <input class="form-control col-md-8" type="text" name="koordinat" value="" id="koordinat" readonly/>&nbsp;
            <input class="btn btn-primary col-md-2 ladda-button" data-style="zoom-in" id="save" type="button" name="save" value="Simpan"/>
        </div>
    {{-- </form> --}}
    <!--End::Dashboard 6-->
</div>
<!-- end:: Content -->

<script>

var map; // Global declaration of the map
// var iw = new google.maps.InfoWindow(); // Global declaration of the infowindow
var lat_longs = new Array();
var markers = new Array();
var drawingManager;
const ajaxSource = "{{url('')}}"+"/"+"gudang";
const id_gudang = "{{$id_gudang}}";

function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: {lat: 24.886, lng: -70.268},
        mapTypeId: 'terrain'
    });

    // Define the LatLng coordinates for the polygon's path.
    var triangleCoords = [
        {lat: 25.774, lng: -80.190},
        {lat: 18.466, lng: -66.118},
        {lat: 32.321, lng: -64.757},
        {lat: 25.774, lng: -80.190}
    ];

    // Construct the polygon.
    var bermudaTriangle = new google.maps.Polygon({
        paths: triangleCoords,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
    });
    bermudaTriangle.setMap(map);
}

function initialize(polygon) {
    const myLatlng = new google.maps.LatLng(-7.1546369, 112.640216);
    const myOptions = {
        zoom: 18,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("layoutGudang"), myOptions);
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        },
        polygonOptions: {
            editable: true  
        }
    });
    drawingManager.setMap(map);

    var layoutArea = new google.maps.Polygon({
        paths: polygon,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        editable: true
    });

    layoutArea.setMap(map);

    google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {
        const newShape = event.overlay;
        newShape.type = event.type;
    });

    google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {
        overlayClickListener(event.overlay);
        const polygon = event.overlay.getPath().getArray();
        // for (var i = 0; i < event.overlay.getPath().getLength(); i++) {
        //     document.getElementById('koordinat').value += polygon.getPath().getAt(i).toUrlValue(6) + "<br>";
        // }
        // polygon.forEach(element => {
        //     console.log('lat:'+element);
        // });
        
        // console.log(event.overlay.getPath().getArray());
        $('#koordinat').val(JSON.stringify(event.overlay.getPath().getArray()));
    });
}

function overlayClickListener(overlay) {
    google.maps.event.addListener(overlay, "mouseup", function (event) {
        const polygon = overlay.getPath().getArray();
        // for (var i = 0; i < event.overlay.getPath().getLength(); i++) {
        //     document.getElementById('koordinat').value += polygon.getPath().getAt(i).toUrlValue(6) + "<br>";
        // }
        $('#koordinat').val(JSON.stringify(overlay.getPath().getArray()));

       
    });
}

// google.maps.event.addDomListener(window, 'load', initialize);

function muatArea() {
    $("#layoutGudang").show();
    $("#noSelectWil").hide();
    const pilih_area = $("#pilih_area").val();
    $.ajax({
        url:ajaxSource+"/load-koordinat/"+pilih_area,
        success:response => {
            const obj = response.data;
            let polygon = [];
            let tempData = [];
            // console.log(JSON.parse(obj));
            // for ( var index=0; index<obj.length; index++ ) {
            //     tempData = JSON.stringify(obj[index]);
            //     // console.log(tempData);
            //     polygon.push(tempData);
            // }

            polygon = JSON.parse(obj);
            // console.log(polygon);
            // polygon = [
            //     {lat: 25.774, lng: -80.190},
            //     {lat: 18.466, lng: -66.118},
            //     {lat: 32.321, lng: -64.757},
            //     {lat: 25.774, lng: -80.190}
            // ];
            initialize(polygon);
        },
        error:response =>{

        }
    });

    //  initialize();
}

$(function () {
    $('#save').click(function (e) {
        e.preventDefault();
        laddaButton = Ladda.create(this);
        laddaButton.start();
        const koordinat = $("#koordinat").val();
        const pilih_area = $("#pilih_area").val();
        $.ajax({
            url: ajaxSource+"/save-map",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:"put",
            data:{
                koordinat:koordinat,
                pilih_area:pilih_area
            },
            beforeSend: function() {
                preventLeaving();
            },
            success:response=>{
                swal.fire("Pemberitahuan", response.message, "success").then(()=>{
                    // $("#koordinat").val("");
                });
            },
            error:response=>{
                $("#save").prop("disabled", false);
                let head = "Maaf",
                    message = "Terjadi kesalahan koneksi",
                    type = "error";
                laddaButton.stop();
                window.onbeforeunload = false;
                $(".se-pre-con").hide();
                if (response["status"] == 401 || response["status"] == 419) {
                    location.reload();
                } else {
                    if (response["status"] != 404 && response["status"] != 500) {
                        let obj = JSON.parse(response["responseText"]);

                        if (!$.isEmptyObject(obj.message)) {
                            if (obj.code > 450) {
                                head = "Maaf";
                                message = obj.message;
                                type = "error";
                            } else {
                                head = "Pemberitahuan";
                                type = "warning";
                                if (!$.isEmptyObject(response.responseJSON.errors)) {
                                    obj = response.responseJSON.errors;
                                    laddaButton.stop();
                                    window.onbeforeunload = false;
                    
                                    const temp = Object.values(obj);
                                    message = "";
                                    temp.forEach(element => {
                                        element.forEach(row => {
                                            message += row + "<br>";
                                        });
                                    });
                                } else {
                                    message = obj.message
                                }
                            }
                        }
                    }
                    swal.fire(head, message, type);
                }
            }
        });
    });
});

$("#pilih_area").select2({
    allowClear: true,
    placeholder: 'Pilih Area',
    delay: 250,
    ajax: {
        url: ajaxSource + '/load-area/' + id_gudang,
        dataType: 'json',
        processResults: function (response) {
            /*Tranforms the top-level key of the response object from 'items' to 'results'*/
            return {
                results: $.map(response.data, function (item) {
                    return {
                        text: item.nama,
                        id: item.id
                    }
                })
            };
        }
    }
}).on("select2:select", (q) => {
});

$('#pilih_area').on('change', function() {
    if ($(this).val()=='wil1') {
        mapsArea1();
    } else if ($(this).val()=='wil2') {
        mapsArea2();
    } else if ($(this).val()=='drawManual') {
        drawmanual();
    }
});




</script>
@endsection