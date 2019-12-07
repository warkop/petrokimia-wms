function mapsArea1() {
    document.querySelector("#noSelectWil").style.display = "none";
    document.querySelector("#layoutGudang").style.display = "block";
    document.querySelector("#labelnoSelectWil").style.display = "none";
    var map = new GMaps({
        div: '#layoutGudang',
        lat: -7.297250,
        lng: 112.758369
    });

    var path = [
        [-7.297250, 112.758369],
        [-7.397250, 112.858369],
        [-7.497250, 112.958369],
        [-7.597250, 112.888369]
    ];

    var polygon = map.drawPolygon({
        paths: path,
        strokeColor: '#BBD8E9',
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#BBD8E9',
        fillOpacity: 0.6
    });
}


function mapsArea2() {
    document.querySelector("#noSelectWil").style.display = "none";
    document.querySelector("#layoutGudang").style.display = "block";
    document.querySelector("#labelnoSelectWil").style.display = "none";
    var map = new GMaps({
        div: '#layoutGudang',
        lat: -6.904970,
        lng: 112.065530
    });

    var path = [
        [-6.504970, 112.165530],
        [-6.804970, 112.25530],
        [-6.704970, 112.365530],
        [-6.604970, 112.465530]
    ];

    var polygon = map.drawPolygon({
        paths: path,
        strokeColor: '#BBD8E9',
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#BBD8E9',
        fillOpacity: 0.6
    });
}

function clearWil() {
    document.querySelector("#layoutGudang").style.display = "none";
    document.querySelector("#noSelectWil").style.display = "block";
    // document.querySelector("#labelnoSelectWil").style.display = "block";
}





// function drawmanual() {
//     var map; // Global declaration of the map
//     var iw = new google.maps.InfoWindow(); // Global declaration of the infowindow
//     var lat_longs = new Array();
//     var markers = new Array();
//     var drawingManager;

//     document.querySelector("#layoutGudang").style.display = "block";
//     var myLatlng = new google.maps.LatLng(40.9403762, -74.1318096);
//     var myOptions = {
//         zoom: 13,
//         center: myLatlng,
//         mapTypeId: google.maps.MapTypeId.ROADMAP
//     }
//     map = new google.maps.Map(document.getElementById("layoutGudang"), myOptions);
//     drawingManager = new google.maps.drawing.DrawingManager({
//         drawingMode: google.maps.drawing.OverlayType.POLYGON,
//         drawingControl: true,
//         drawingControlOptions: {
//             position: google.maps.ControlPosition.TOP_CENTER,
//             drawingModes: [google.maps.drawing.OverlayType.POLYGON]
//         },
//         polygonOptions: {
//             editable: true
//         }
//     });
//     drawingManager.setMap(map);

//     google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {
//         var newShape = event.overlay;
//         newShape.type = event.type;
//     });
// }

// drawmanual();

// google.maps.event.addDomListener(window, 'load', drawmanual);
