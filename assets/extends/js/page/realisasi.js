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
    // console.log(rows);
    let html = 
        `<tr class="material_baris" id="baris-material-${rows}">
            <td>
                <select class="form-control m-select2 kt_select2_material material_pilih" onchange="checkMaterial(this)" name="id_material[]"
                    aria-placeholder="Pilih material" id="namamaterial-${rows}" style="width: 100%;">
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="material-tambah-${rows}" name="material_tambah[]" placeholder="Jumlah bertambah" maxlength="10">
            </td>
            <td>
                <input type="text" class="form-control" id="material-kurang-${rows}" name="material_kurang[]" placeholder="Jumlah berkurang" maxlength="10">
            </td>
            <td>
                <button class="btn btn-danger btn-md btn-block" onclick="hapusMaterial(${rows})"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`;

    $("#table_material tbody").append(html);
    $('#namamaterial-' + rows).select2({
        placeholder: "Pilih Material"
    });

    getMaterial(3, "#namamaterial-" + rows);

    protectNumber(`#material-tambah-${rows}`, 10);
    protectNumber(`#material-kurang-${rows}`, 10);
}

function tambahHousekeeper() {
    const tableId = "#table_housekeeper";
    const rows = $(tableId +" .housekeeper_baris").length+1;
    let html =
        `<div class="housekeeper_baris" id="baris-housekeeper-${rows}">
            <div class="col-3">
                <label class="boldd-500">Pilih Housekeeper</label>
                <select class="form-control m-select2 kt_select2_housekeeping housekeeper_pilih" id="namahousekeeper-${rows}" onchange="check(this)" name="housekeeper[]" aria-placeholder="Pilih Housekeeper" style="width: 100%;">
                </select>
            </div>
            <div class="col-9 col-form-label">
                <label class="boldd-500" style="transform: translateY(-.6rem);">Pilih Area Kerja</label>
                <div class="col-12">
                    <div class="row form-group mb-0 mb2">
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 1
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 2
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 3
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 4
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 5
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 6
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 7
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 5
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 6
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 7
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                <input type="checkbox"> Area 5
                                <span></span>
                            </label>
                        </div>
                        <div class="col-2 mb1 text-left">
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#kt_modal_1"> Tambah Area</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    $("#table_housekeeper").append(html);
    $('#namahousekeeper-'+rows).select2({
        placeholder: "Pilih Housekeeper"
    });

    getHouseKeeper(id_rencana, "#namahousekeeper-" + rows);

    protectNumber(`#housekeeper-tambah-${rows}`, 10);
    protectNumber(`#housekeeper-kurang-${rows}`, 10);
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

function hapusMaterial(id) {
    if (id != '') {
        $("#baris-material-" + id).remove();
    } else {
        $(".material_baris").remove();
    }
}

function hapusHousekeeper(id) {
    if (id != '') {
        $("#baris-housekeeper-" + id).remove();
    } else {
        $(".housekeeper_baris").remove();
    }
}

function checkMaterial(target) {
    let lokasi = $('.material_pilih');
    let chosen = false;

    const id_target = $(target).attr('id').replace('-', '');
    for (let i = 0; i < lokasi.length; i++) {
        if ($(target).val() != "" && $(target).attr('id') != $(lokasi[i]).attr('id') && $(target).val() == $(lokasi[i]).val()) {
            chosen = true;
        }
    }
    if (chosen == true) {
        $(target).val('').trigger('change.select2');
        swal.fire('Pemberitahuan', 'Material sudah dipilih. Silahkan Pilih material lain!', 'error');
        $("#namamaterial-" + id_target).val("");
        console.log(id_target)
    }
}

function check(target) {
    let lokasi = $('.housekeeper_pilih');
    let chosen = false;

    const id_target = $(target).attr('id').replace('-', '');
    for (let i = 0; i < lokasi.length; i++) {
        if ($(target).val() != "" && $(target).attr('id') != $(lokasi[i]).attr('id') && $(target).val() == $(lokasi[i]).val()) {
            chosen = true;
        }
    }
    if (chosen == true) {
        $(target).val('').trigger('change.select2');
        swal.fire('Pemberitahuan', 'Housekeeper sudah dipilih. Silahkan Pilih housekeeper lain!', 'error');
        console.log(id_target)
        $("#namahousekeeper-" + id_target).val("");
    }
}

function getHouseKeeper(id_rencana, target) {
    $.ajax({
        url: ajaxSource + '/realisasi/get-housekeeper/'+id_rencana,
        success: (res) => {
            const obj = res.data;

            let html = `<option value="">Pilih HouseKeeper</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id_tkbm}">${item.nama}</option>`;
            });

            $(target).html(html);

        },
        error: (err, oo, pp) => {

        }
    });
}