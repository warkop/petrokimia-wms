
const id_gudang = "{{$id_gudang}}";
const id_aktivitas_harian = "{{$id_aktivitas_harian}}";

let datatable,
    tableTarget = "#kt_table_1",
    ajaxUrl = baseUrl + "penerimaan-gp",
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;

    $(document).ready(()=>{
        $("#btn_save").on("click", function(e) {
            e.preventDefault();
            laddaButton = Ladda.create(this);
            laddaButton.start();
            simpan();
        });
    })


    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });

    function tambah(obj='') {
        const tableId = "table_produk";
        const rows = document.getElementById(tableId).getElementsByTagName("div").length;
        @if ($aktivitasHarian->approve == null)
            $("#table_produk").append(`
            <div class="row mb2 produk_baris" id="baris-produk-${rows}">
                <div class="col-3">
                    <label class="boldd-500">Pilih Produk</label>
                    <select class="form-control select2Custom m-select2" id="produk-${rows}" name="produk[]" aria-placeholder="Pilih Produk" style="width: 100%;">
                        <option disabled selected>Pilih Produk</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="boldd-500">Jumlah (Ton)</label><br>
                    <input type="text" id="jumlah-${rows}" name="jumlah[]" class="form-control" placeholder="Jumlah">
                </div>
                <div class="col-5">
                    <label class="boldd-500">Keluhan</label>
                    <textarea class="form-control" id="keluhan-${rows}" name="keluhan[]" rows="2"></textarea>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button href="javascript:void(0)" type="button" class="btn btn-danger cursor pointer btn-elevate btn-icon button_hapus" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
                </div>
            </div>
            `);
            
            loadProduk(rows, `#produk-${rows}`, obj)
            $(`#produk-${rows}`).attr("readonly", false);
        @else
            $("#table_produk").append(`
            <div class="row mb2 produk_baris" id="baris-produk-${rows}">
                <div class="col-3">
                    <label class="boldd-500">Pilih Produk</label>
                    <select class="form-control select2Custom m-select2" id="produk-${rows}" name="produk[]" aria-placeholder="Pilih Produk" style="width: 100%;">
                        <option disabled selected>Pilih Produk</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="boldd-500">Jumlah</label><br>
                    <input readonly type="text" id="jumlah-${rows}" name="jumlah[]" class="form-control" placeholder="Jumlah">
                </div>
                <div class="col-5">
                    <label class="boldd-500">Keluhan</label>
                    <textarea readonly class="form-control" id="keluhan-${rows}" name="keluhan[]" rows="2"></textarea>
                </div>
                <div class="col-2">
                    <label class="visibility-hide">Area</label><br>
                    <button href="javascript:void(0)" type="button" class="btn btn-danger cursor pointer btn-elevate btn-icon button_hapus" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Hapus"><i class="flaticon-delete"></i> </button>
                </div>
            </div>
            `);
            
            loadProduk(rows, `#produk-${rows}`, obj, false)

            // $(`#produk-${rows}`).attr("readonly", true);
        @endif
        $('.select2Custom').select2({
            placeholder: "Pilih Produk",
            dropdownParent:$(`#baris-produk-${rows}`)
        });
        
         
    }

    $("body").on('click', '.button_hapus', function (e) {
        $(this).parent().parent().remove();
    });

    function loadProduk(no, target, produk='', edit=true) {
        $.ajax({
            url:  baseUrl + "penerimaan-gp" + "/" + "get-produk/"+id_aktivitas_harian,
            success: res => {
                const obj = res.data;
                let html = `<option value="">Pilih Produk</option>`;
                obj.forEach((item, index) => {
                    html += `<option value="${item.id_material}">${item.nama}</option>`;
                });

                $(target).html(html);
                $("#produk-"+no).val(produk.id_material);
                $("#jumlah-"+no).val(produk.jumlah);
                $("#keluhan-"+no).html(produk.keluhan);

                if (edit == false) {
                    $("#produk-"+no).select2({
                    disabled: true
                    });
                }
            },
            error: () => {}
        });
    }

    function simpan() {
        let data = $("#form1").serializeArray();
        
        $.ajax({
            type: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: ajaxUrl + "/" + id_aktivitas_harian,
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

                $('#kt_keluhan').modal('hide');
                swal.fire('Ok', obj.message, 'success');
            },
            error: function (response) {
                $("#btn_save").prop("disabled", false);
                let head = 'Maaf',
                    message = 'Terjadi kesalahan koneksi',
                    type = 'error';
                laddaButton.stop();
                window.onbeforeunload = false;
                $('.btn_close_modal').removeClass('hide');
                $('.se-pre-con').hide();

                if (response['status'] == 401 || response['status'] == 419) {
                    location.reload();
                } else {
                    if (response['status'] != 404 && response['status'] != 500) {
                        let obj = JSON.parse(response['responseText']);

                        if (!$.isEmptyObject(obj.message)) {
                            if (obj.code > 450) {
                                head = 'Maaf';
                                message = obj.message;
                                type = 'error';
                            } else {
                                head = 'Pemberitahuan';
                                type = 'warning';

                                obj = response.responseJSON.errors;
                                laddaButton.stop();
                                window.onbeforeunload = false;
                                $('.btn_close_modal').removeClass('hide');
                                $('.se-pre-con').hide();

                                const temp = Object.values(obj);
                                message = '';
                                temp.forEach(element => {
                                    element.forEach(row => {
                                        message += row + "<br>"
                                    });
                                });
                            }
                        }
                    }

                    swal.fire(head, message, type);
                }
            }
        });
    }

    function loadKeluhan(){
        $("#kt_keluhan").modal("show");
        $.ajax({
            url: "{{ url('penerimaan-gp') }}/get-produk/"+id_aktivitas_harian,
            success:res=>{
                
                if (res.keluhan != '') {
                    $("#table_produk").html('');
                    const obj = res.keluhan;
                    obj.forEach(element => {
                        tambah(element)
                    });
                } else {
                    document.getElementById("belumada").remove();
                }
            },
            error:response=>{
                let head = 'Maaf',
                    message = 'Terjadi kesalahan koneksi',
                    type = 'error';
                window.onbeforeunload = false;
                $('.btn_close_modal').removeClass('hide');
                $('.se-pre-con').hide();

                if (response['status'] == 401 || response['status'] == 419) {
                    location.reload();
                } else {
                    if (response['status'] != 404 && response['status'] != 500) {
                        let obj = JSON.parse(response['responseText']);

                        if (!$.isEmptyObject(obj.message)) {
                            if (obj.code > 450) {
                                head = 'Maaf';
                                message = obj.message;
                                type = 'error';
                            } else {
                                head = 'Pemberitahuan';
                                type = 'warning';
                                obj = response.responseJSON.errors;
                                message = '';
                                if (obj == null) {
                                    message = response.responseJSON.message;
                                } else {
                                    const temp = Object.values(obj);
                                    
                                    temp.forEach(element => {
                                        element.forEach(row => {
                                            message += row + "<br>"
                                        });
                                    });
                                }

                                // laddaButton.stop();
                                window.onbeforeunload = false;
                                $('.btn_close_modal').removeClass('hide');
                                $('.se-pre-con').hide();
                            }
                        }
                    }

                    swal.fire(head, message, type);
                }
            }
        });
    }

    function approve() {
         swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang sudah disetujui tidak bisa dibatalkan.",
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Tidak',
            confirmButtonText: 'Ya!'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('penerimaan-gp') }}/"+id_aktivitas_harian,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method:"PATCH",
                    success:res=>{
                        swal.fire('Ok', "Data berhasil disimpan", 'success').then(()=>{
                            location.href = ajaxSource;
                        });
                    },
                    error:(response)=>{
                        $("#btn_save").prop("disabled", false);
                        let head = 'Maaf',
                            message = 'Terjadi kesalahan koneksi',
                            type = 'error';
                        // laddaButton.stop();
                        window.onbeforeunload = false;
                        $('.btn_close_modal').removeClass('hide');
                        $('.se-pre-con').hide();

                        if (response['status'] == 401 || response['status'] == 419) {
                            location.reload();
                        } else {
                            if (response['status'] != 404 && response['status'] != 500) {
                                let obj = JSON.parse(response['responseText']);

                                if (!$.isEmptyObject(obj.message)) {
                                    if (obj.code > 450) {
                                        head = 'Maaf';
                                        message = obj.message;
                                        type = 'error';
                                    } else {
                                        head = 'Pemberitahuan';
                                        type = 'warning';
                                        obj = response.responseJSON.errors;
                                        message = '';
                                        // console.log(obj)
                                        if (obj == null) {
                                            message = response.responseJSON.message;
                                        } else {
                                            const temp = Object.values(obj);
                                            
                                            temp.forEach(element => {
                                                element.forEach(row => {
                                                    message += row + "<br>"
                                                });
                                            });
                                        }

                                        // laddaButton.stop();
                                        window.onbeforeunload = false;
                                        $('.btn_close_modal').removeClass('hide');
                                        $('.se-pre-con').hide();
                                    }
                                }
                            }

                            swal.fire(head, message, type);
                        }
                    }
                });
            }
        });
    }

    function loadArea(id_material) {
        $.ajax({
            url:ajaxSource+'/get-area/'+id_gudang+"/"+id_material+"/"+id_aktivitas_harian,
            beforeSend:()=>{
                $("#tempat_card").html(`
                <div class="card br">
                    <div class="wrapper">
                        <div class="profilePic animate din"></div>
                        <div class="comment br animate w80"></div>
                        <div class="comment br animate"></div>
                        <div class="comment br animate"></div>
                    </div>
                <div>
                `);
            },
            success:(response) => {
                let tampung_nama = "";
                let temp_nama = "";
                let areanya = "";
                let temp = "";
                let i=1;
                response.forEach(element => {
                    temp_nama = `
                        <div class="card-header" id="heading-${i}">
                            <div class="card-title" data-toggle="collapse show" data-target="#collapse-${i}"
                                aria-expanded="true" aria-controls="collapse-${i}">
                                <i class="flaticon2-shelter"></i> Area ${element.nama_area}
                            </div>
                        </div>
                    `;

                    areanya = "";
                    areanya += `
                        <div class="kt-widget4__item border-bottom-dash mt1">
                            <div class="kt-widget4__info">
                                <h6 class="kt-widget4__username">
                                    ${helpDateFormat(element.tanggal, "mi")}
                                </h6>
                                <p class="kt-widget4__text boldd">
                                    ${element.jumlah} Ton
                                </p>
                            </div>
                        </div>`;
                    if (!$.isEmptyObject(temp_nama)) {
                        temp += `
                                <div class="card">
                                    <div id="collapse-${i}" class="collapse show" aria-labelledby="heading-${i}" data-parent="#tempat_card">
                                        <div class="card-body">
                                        ${temp_nama}
                                        ${areanya}
                                        </div>
                                    </div>
                                </div>`;
                    }
                    i++;
                });
                $("#tempat_card").html(temp);
            },
            error:response => {
                let head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
                window.onbeforeunload = false;
                $('.btn_close_modal').removeClass('hide');
                $('.se-pre-con').hide();

                if (response['status'] == 401 || response['status'] == 419) {
                    location.reload();
                } else {
                    if (response['status'] != 404 && response['status'] != 500) {
                        let obj = JSON.parse(response['responseText']);

                        if (!$.isEmptyObject(obj.message)) {
                            if (obj.code > 450) {
                                head = 'Maaf';
                                message = obj.message;
                                type = 'error';
                            } else {
                                head = 'Pemberitahuan';
                                type = 'warning';
                                obj = response.responseJSON.errors;
                                message = '';
                                if (obj == null) {
                                    message = response.responseJSON.message;
                                } else {
                                    const temp = Object.values(obj);
                                    message = '';
                                    temp.forEach(element => {
                                        element.forEach(row => {
                                            message += row + "<br>"
                                        });
                                    });
                                }

                                window.onbeforeunload = false;
                                $('.btn_close_modal').removeClass('hide');
                                $('.se-pre-con').hide();
                            }
                        }
                    }

                    swal.fire(head, message, type);
                }
            }
        });
    }

function loadDetail() {
    $("#modal_detail").modal({backdrop: "static", keyboard: false},"show");

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/list-keluhan/"+ id_aktivitas_harian,
        beforeSend: function () {
        preventLeaving();
            $(".btn_close_modal").addClass("hide");
            $(".se-pre-con").show();
        },
        success: function (response) {
            window.onbeforeunload = false;
            $(".btn_close_modal").removeClass("hide");
            $(".se-pre-con").hide();

            let obj_produk = response.data;

            let text = "";
            let i=1;
            obj_produk.forEach(element => {
                text += `
                    <tr>
                        <td>${i}</td>
                        <td>${element.material.nama}</td>
                        <td>${element.jumlah}</td>
                        <td>${element.keluhan} Ton</td>
                    </tr>
                `;
                i++;
            });
            $("#tubuh_produk").html(text);
        },
        error: function (response) {
            let head = "Maaf",
                message = "Terjadi kesalahan koneksi",
                type = "error";
            window.onbeforeunload = false;
            $(".btn_close_modal").removeClass("hide");
            $(".se-pre-con").hide();

            if (response["status"] == 401 || response["status"] == 419) {
                location.reload();
            } else {
                if (response["status"] != 404 && response["status"] != 500) {
                let obj = JSON.parse(response["responseText"]);

                if (!$.isEmptyObject(obj.message)) {
                    if (obj.code > 400) {
                    head = "Maaf";
                    message = obj.message;
                    type = "error";
                    } else {
                    head = "Pemberitahuan";
                    message = obj.message;
                    type = "warning";
                    }
                }
                }

                swal.fire(head, message, type);
            }
        }
    });
}