"use strict";
const ajaxSource = baseUrl+"layout";

function loadArea() {
    $.ajax({
        url: ajaxSource +"/load-area",
        success:response=>{
            const obj = response.data;
            demo6(obj);
        },
        error:response=>{

        }
    });
}
// Class definition
const demo6 =  (data, warna) => {
    const myLatlng = new google.maps.LatLng(-7.1546369, 112.640216);
    const myOptions = {
        zoom: 17,
        center: myLatlng,
        streetViewControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("kt_gmap_1"), myOptions);

    if (data !== null) {
        for (let i = 0; i < data.length; i++) {

            let temp = JSON.parse(data[i].koordinat);

            let polygon = addPoly(temp, data[i].warna);
            polygon.setMap(map);
            addListenersOnPolygon(polygon, data[i]);
        }
    }
}

function addPoly(polygon, warna) {
    var poly = new google.maps.Polygon({
        paths: polygon,
        strokeColor: warna,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: warna,
        fillOpacity: 0.35
    });
    return poly;
};

function addListenersOnPolygon(polygon, data) {
    google.maps.event.addListener(polygon, 'click', function (event) {
        loadData(data.id);
    });
}

function loadData(id) {
    $.ajax({
        url: ajaxSource+'/detail-area/'+id,
        success:(response)=>{
            const obj = response.data;
            let html = "";
            $("#nama").html(obj.area.nama);
            $("#nama_gudang").html(obj.area.gudang.nama);
            $("#kapasitas").html(obj.area.kapasitas);
            $("#terpakai").html(obj.terpakai);
            // for (let i = 0; i < obj.area.area_stok.length; i++) {
            //     const element = obj.area.area_stok[i];
            //     html += `<div class="col-12 mb1">
            //         <p class="boldd-500" id="tanggal">
            //             Tanggal : ${helpDateFormat(element.tanggal, 'li')}
            //         </p>
            //         <p class="boldd-500">
            //             ${element.material.nama} ${element.jumlah} Ton
            //         </p>
            //         <div class="border-pembatas"></div>
            //     </div>`;
            // }

            $("#list").html(html);
        },
        error:(response)=>{

        }
    })
}

$(document).ready(function() {
    loadArea();
});