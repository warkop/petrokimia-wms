"use strict"
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'rencana-harian',
    ajaxSource = ajaxUrl,
    laddaButton;

$(document).ready(()=>{
    getTkbm();
});

function getArea(target) {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-area/',
        success: res => {
            const obj = res.data;

            let html = '';
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
        },
        error: (err, oo, pp) => {

        }
    });
}

function getTkbm() {
    const panjang = $("#selected li").length;
    $.ajax({
        url: ajaxSource + "/get-tkbm/"+1,
        success:res=>{
            const obj = res.data;

            let html = '';
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            console.log(html)
            for (let i=1; i<=panjang; i++) {
                $("#housekeeper-" + i).html(html);
            }
            console.log($("#housekeeper-" + i));
        },
        error:(err, oo, pp)=>{

        }
    });
}