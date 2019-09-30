"use strict";

// Class definition
var KTGoogleMapsDemo = function() {

    var demo6 = function() {
        var map = new GMaps({
            div: '#kt_gmap_1',
            lat: -12.043333,
            lng: -77.028333
        });

        var path = [
            [-12.040397656836609, -77.03373871559225],
            [-12.040248585302038, -77.03993927003302],
            [-12.050047116528843, -77.02448169303511],
            [-12.044804866577001, -77.02154422636042]
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

    return {
        // public functions
        init: function() {
            // default charts
            demo6();
        }
    };
}();

jQuery(document).ready(function() {
    KTGoogleMapsDemo.init();
});