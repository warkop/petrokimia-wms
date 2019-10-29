"use strict"
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'rencana-harian',
    ajaxSource = ajaxUrl,
    laddaButton;

$(document).ready(()=>{
    $('#btn_save').on('click', function (e) {
        e.preventDefault();
        laddaButton = Ladda.create(this);
        laddaButton.start();
        simpan();
    });
});

function getArea() {
    const target = "#kt_select2_area";
    const id_gudang = $("#kt_select2_gudang").val();
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-area/'+id_gudang,
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

function pilihArea() {
    const area = $("#kt_select2_area").val();
    const id = $("#id_row").val();
    let collect_area = [];
    const text_area = $("#kt_select2_area option:selected").each(function () {
        let $this = $(this);
        if ($this.length) {
            let selText = $this.text();
            collect_area.push(selText);
        }
    });
    const target = "#tempat_area-"+id;
    const panjang = area.length;
    let html = '';
    for (let i = 0; i<panjang; i++) {
        if ($('#tempat_area-' + id + " input[name='area_housekeeper']").length < 1) {
            html += `<div class="col-4 mb1">
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                            <input type="checkbox" name="area_housekeeper[${id-1}][]" value="${area[i]}">${collect_area[i]}
                            <span></span>
                        </label>
                    </div>`;

        }
    }


    $(target).append(html);
}

// function getTkbm() {
//     const panjang = $("#selected li").length;
//     $.ajax({
//         url: ajaxSource + "/get-tkbm/"+1,
//         success:res=>{
//             const obj = res.data;

//             let html = '';
//             obj.forEach((item, index) => {
//                 html += `<option value="${item.id}">${item.nama}</option>`;
//             });

//             for (let i=1; i<=panjang; i++) {
//                 $("#housekeeper-" + i).html(html);
//             }
//         },
//         error:(err, oo, pp)=>{

//         }
//     });
// }

function tambahMaterial(id_realisasi='', id_material='', bertambah='', berkurang='') {
    const tableId = "table_material";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;
    // console.log(rows);
    let html = 
        `<tr class="material_baris" id="baris-material-${rows}">
            <td>
                <select class="form-control m-select2 kt_select2_material material_pilih" onchange="checkMaterial(this)" name="material[]"
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

    
    getMaterial(3, "#namamaterial-" + rows, rows, id_material, bertambah, berkurang);

    protectNumber(`#material-tambah-${rows}`, 10);
    protectNumber(`#material-kurang-${rows}`, 10);
}

function fill(id_material, bertambah, berkurang, callback) {

}

function tambahHousekeeper(id_tkbm = '') {
    const tableId = "#table_housekeeper";
    const rows = $(tableId +" .housekeeper_baris").length+1;
    let html =
        `<div class="housekeeper_baris" id="baris-housekeeper-${rows}">
            <div class="col-12">
                <label class="boldd-500">Pilih Housekeeper</label>
                <select class="form-control m-select2 kt_select2_housekeeping housekeeper_pilih" id="namahousekeeper-${rows}" onchange="check(this)" name="housekeeper[]" aria-placeholder="Pilih Housekeeper" style="width: 100%;">
                </select>
            </div>
            <div class="col-12 col-form-label">
                <label class="boldd-500" style="transform: translateY(-.6rem);">Pilih Area Kerja</label>
                <div class="col-12">
                    <div class="row form-group mb-0 mb2" id="tempat_area-${rows}">
                        
                        
                    </div>
                    <div class="mb1 text-left">
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#kt_modal_1" onclick="tambahArea(${rows})"> Tambah Area</button>
                    </div>
                </div>
            </div>
        </div>`;

    $("#table_housekeeper").append(html);
    $('#namahousekeeper-'+rows).select2({
        placeholder: "Pilih Housekeeper"
    });

    getHouseKeeper(id_rencana, "#namahousekeeper-" + rows, rows, id_tkbm);

    protectNumber(`#housekeeper-tambah-${rows}`, 10);
    protectNumber(`#housekeeper-kurang-${rows}`, 10);
}

function tambahArea(id) {
    $("#kt_select2_area").val("").trigger('change.select2');
    $("#kt_select2_area").find('option')
        .remove()
        .end();
    const target = "#kt_select2_gudang";
    $("#id_row").val(id);
    $.ajax({
        url: ajaxSource + '/get-gudang',
        success:(res)=>{
            const obj = res.data;

            let html = `<option value="">Pilih Gudang</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
        },
        error:()=>{

        }
    });
}

function getMaterial(kategori, target, number='', id_material='', bertambah='', berkurang='') {
    $.ajax({
        url: ajaxSource + '/get-material/' + kategori,
        success:(res) => {
            const obj = res.data;

            let html = `<option value="">Pilih Material</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });
            
            $(target).html(html);

            $(target).val(id_material).trigger('change');
            $("#material-tambah-" + number).val(bertambah);
            $("#material-kurang-" + number).val(berkurang);
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

function getHouseKeeper(id_rencana, target, number='', id_tkbm='', id_area='') {
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

const simpan = () => {
    $("#btn_save").prop("disabled", true);
    let data = $("#form1").serializeArray();
    $.ajax({
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl+"/realisasi/"+id_rencana,
        data: data,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success: function (response) {
            laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj = response;

            if (obj.status == "OK") {
                swal.fire('Ok', obj.message, 'success').then(() => {
                    window.location = ajaxSource;
                }).catch(() => {

                });
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }

        },
        error: function (response) {
            const head = 'Pemberitahuan';
            const type = 'warning';
            const obj = response.responseJSON.errors;
            laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            const temp = Object.values(obj);
            let message = '';
            temp.forEach(element => {
                element.forEach(row => {
                    message += row + "<br>"
                });
            });
            swal.fire(head, message, type);
        }
    });
}
