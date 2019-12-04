// "use strict";

// var KTGoogleMapsDemo = function() {

//     var demo6 = function() {
//         var map = new GMaps({
//             div: '#layoutGudang',
//             lat: -7.297250,
//             lng: 112.758369
//         });

//         var path = [
//             [-7.297250, 112.758369],
//             [-7.397250, 112.858369],
//             [-7.497250, 112.958369],
//             [-7.597250, 112.888369]
//         ];

//         var polygon = map.drawPolygon({
//             paths: path,
//             strokeColor: '#BBD8E9',
//             strokeOpacity: 1,
//             strokeWeight: 3,
//             fillColor: '#BBD8E9',
//             fillOpacity: 0.6
//         });
//     }

//     return {
//         init: function() {
//             demo6();
//         }
//     };
// }();

// jQuery(document).ready(function() {
//     KTGoogleMapsDemo.init();
// });

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
    document.querySelector("#labelnoSelectWil").style.display = "block";
}