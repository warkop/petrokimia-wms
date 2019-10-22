"use strict"

$(document).ready(()=>{
    // getTkbm(1, "")
});

function getTkbm(id_job_desk, target, id_rencana = '', id_tkbm = '') {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-tkbm/' + id_job_desk,
        success: res => {
            const obj = res.data;

            let html = `<option value="">Pilih Housekeeper</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);

            if (id_rencana != '' && id_job_desk != 1) {
                getRencanaTkbm(id_job_desk, id_rencana, target);
                // setTimeout(() => {
                //     console.log(id_tkbm);
                //     getRencanaTkbmArea(id_rencana, id_tkbm);
                // }, 1000);
            }
        },
        error: (err, oo, pp) => {

        }
    });
}