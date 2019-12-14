"use strict";
const ajaxSource = baseUrl+"layout";

function loadArea() {
    console.log(ajaxSource);
    $.ajax({
        url: ajaxSource +"/load-area",
        success:response=>{
            // const obj = response.data;
            demo6(obj);
        },
        error:response=>{

        }
    });
}
// Class definition
const demo6 =  polygons => {
    var map = new GMaps({
        div: '#kt_gmap_1',
        lat: -7.1546369, 
        lng: 112.640216,
        zoom: 17
    });

    let path = [];
    if (polygons !== null) {
        for (let i = 0; i < polygons.length; i++) {
            let temp = polygons[i].koordinat;
            // console.log(polygons[i].koordinat);
            path.push(temp);
        }
    }

    var polygon = map.drawPolygon({
        paths: path,
        strokeColor: '#BBD8E9',
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#BBD8E9',
        fillOpacity: 0.6
    });

    polygon.setMap(map);
}

$(document).ready(function() {
    loadArea();
});