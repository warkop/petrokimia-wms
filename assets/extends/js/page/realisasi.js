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

            for (let i=1; i<=panjang; i++) {
                $("#housekeeper-" + i).html(html);
            }
        },
        error:(err, oo, pp)=>{

        }
    });
}

function tambahMaterial() {
    const tableId = "table_material";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;
    let html = 
        `<tr id="baris-material-${rows}">
            <td>
                <select class="form-control m-select2 kt_select2_material" name="id_material[]"
                    aria-placeholder="Pilih kategori" id="nama-material-${rows}" style="width: 100%;">
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="material-tambah-${rows}" name="material_tambah[]" placeholder="Jumlah bertambah">
            </td>
            <td>
                <input type="text" class="form-control" id="material-kurang-${rows}" name="material_kurang[]" placeholder="Jumlah berkurang">
            </td>
            <td>
                <button class="btn btn-danger btn-md btn-block" onclick="hapusMaterial(${rows})"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`;

    $("#table_material tbody").append(html);
    $('.kt_select2_material').select2({
        placeholder: "Pilih Material"
    });

    getMaterial(3, "#nama-material-" + rows);
}

function getMaterial(kategori, target) {
    $.ajax({
        url: ajaxSource + '/get-material/' + kategori,
        success:(res) => {
            const obj = res.data;

            let html = `<option value="">Pilih Material</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);

        },
        error: (err, oo, pp) =>{

        }
    });
}
