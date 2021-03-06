"use strict";
let datatable,
  tableTarget = "#kt_table_1",
  ajaxUrl = baseUrl + "gudang",
  ajaxSource = ajaxUrl,
  totalFiles = 0,
  completeFiles = 0,
  laddaButton;

Dropzone.autoDiscover = false;
const dropzoneOptions = {
  url: ajaxSource + "/stock-adjustment/upload/" + id_gudang,
  type: "POST",
  params: {
    _token: $('meta[name="csrf-token"]').attr("content")
  },
  parallelUploads: 1000,
  maxFiles: 1,
  addRemoveLinks: true,
  dictDefaultMessage: "Seret File atau klik disini untuk mengunggah",
  acceptedFiles: ".jpg,.png,.jpeg,.gif",
  autoProcessQueue: false,
  init: function() {
    this.on("addedfile", function(file) {
      if (!file.type.match("image.*")) {
        // alert("Upload Image Only!");
        // return false;
      }
    });
    this.on("success", function(file) {
      datatable.api().ajax.reload();
      if (completeFiles === totalFiles) {
        /* window["myDropzone"+i+"_"+val+"_1"].removeAllFiles(); */
      }
    });
  }
};

const myDropzone = new Dropzone("#m-dropzone-one", dropzoneOptions);

$(document).ready(function() {
  load_table();
  // initFancybox('.fancybox', '.fancybox-effects-a');
  if (typeof datatable !== "undefined") {
    datatable.on("draw.dt", function() {
      $("[data-toggle=kt-tooltip]").tooltip();
    });
  }

  $("#btn_save").on("click", function(e) {
    e.preventDefault();
    laddaButton = Ladda.create(this);
    laddaButton.start();
    simpan();
  });

  $(".input-enter").on("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
      $("#btn_save").click();
    }
  });

  // protectNumber(".produk_jumlah");
  protectNumber(".pallet_jumlah");
});

const load_table = function() {
  datatable = $(tableTarget);
  // begin first table
  datatable.dataTable({
    bDestroy: true,
    processing: true,
    serverSide: true,
    responsive:true,
    ajax: {
      url: ajaxSource + "/stock-adjustment/" + id_gudang,
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
    },
    sPaginationType: "full_numbers",
    aoColumns: [
      {
        mData: "id"
      },
      {
        mData: "tanggal"
      },
      {
        mData: null
      },
      {
        mData: "id"
      }
    ],
    aaSorting: [[1, "asc"]],
    lengthMenu: [10, 25, 50, 75, 100],
    pageLength: 10,
    aoColumnDefs: [
      {
        aTargets: [0],
        mData: "id",
        mRender: function(data, type, full, draw) {
          let row = draw.row;
          let start = draw.settings._iDisplayStart;
          let length = draw.settings._iDisplayLength;

          let counter = start + 1 + row;

          return counter;
        }
      },
      {
        className: "text-center",
        targets: -1,
        title: "Actions",
        orderable: false,
        render: function(data, type, full, meta) {
          return `
                <button type = "button" onclick="detail(${full.id})" class="btn btn-primary btn-elevate btn-icon" data-toggle="modal" data-target="#modal_detail" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Detail">
                    <i class="flaticon-medical"></i></button>`;
        }
      },
      {
        className: "text-center",
        targets: -2,
        orderable: false,
        render: function(data, type, full, meta) {
          let link = "";
          let image = "Tidak ada gambar";
          if (full.foto != null) {
            link =
              baseUrl +
              /watch/ +
              full.foto +
              "?" +
              "un=" +
              full.id +
              "&ctg=material&src=" +
              full.foto;
              
            image =
              '<a target="_blank" class="fancybox fancybox-effects-a" data-fancybox="file-' +
              full.id +
              '" data-caption="' +
              full.foto +
              '" rel="ligthbox" href="' +
              link +
              '"><img class="img-responsive" width="100px" src="' +
              link +
              '" alt=""></a>';
            }

          return image;
        }
      }
    ],
    fnHeaderCallback: function(nHead, aData, iStart, iEnd, aiDisplay) {
      $(nHead)
        .children("th:nth-child(1), th:nth-child(2), th:nth-child(3)")
        .addClass("text-center");
    },
    fnFooterCallback: function(nFoot, aData, iStart, iEnd, aiDisplay) {
      $(nFoot)
        .children("th:nth-child(1), th:nth-child(2), th:nth-child(3)")
        .addClass("text-center");
    },
    fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $(nRow)
        .children(
          "td:nth-child(1),td:nth-child(2),td:nth-child(3),td:nth-child(4)"
        )
        .addClass("text-center");
    },
    fnDrawCallback: function(settings) {
      $('[data-toggle="kt-tooltip"]').tooltip();
    }
  });
};

function tambahProduk(id = "", tipe = "", jumlah = "") {
  const tableId = "table_produk";
  const rows = document.getElementById(tableId).getElementsByTagName("tr")
    .length;

  let html = `<tr class="produk_baris" id="baris-produk-${rows}">
                    <td>${rows}</td>
                    <td id="tempat-produk-${rows}">
                        <select class="form-control m-select2" id="produk-${rows}" name="produk[]" onchange="checkProduk(this)" aria-placeholder="Pilih Produk" style="width: 100%;">
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="jenis_produk[]" id="jenis-produk-${rows}" style="width: 100%;">
                          <option value="1">Normal</option>
                          <option value="2">Rusak</option>
                        </select>
                    </td>
                    <td id="tempat-area-${rows}">
                        <select class="form-control m-select2 pilih_area" id="area-${rows}" name="area[]" aria-placeholder="Pilih Area" style="width: 100%;">
                        </select>
                    </td>
                    <td>
                        <input class="form-control pilih_tanggal" id="tanggal-${rows}" name="tanggal_produksi[]" aria-placeholder="Pilih Tanggal Produksi" style="width: 100%;">
                    </td>
                    <td>
                        <select class="form-control" name="action_produk[]" id="produk-status-${rows}" style="width: 100%;">
                        <option value="1">Mengurangi</option>
                        <option value="2">Menambah</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="produk_jumlah[]" id="produk-jumlah-${rows}" class="form-control" placeholder="Masukkan jumlah">
                    </td>
                    <td>
                        <textarea name="produk_alasan[]" id="produk-alasan-${rows}" class="form-control" placeholder="Masukkan alasan"></textarea>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-elevate btn-icon btn-sm" onclick="hapusProduk(${rows})"><i class="la la-trash"></i></button>
                    </td>
                </tr>`;
  $("#table_produk tbody").append(html);
  $(`#produk-${rows}`).select2({
    placeholder: "Pilih Produk",
    dropdownParent: $(`#tempat-produk-${rows}`)
  });
  $(`#area-${rows}`).select2({
    placeholder: "Pilih Area",
    dropdownParent: $(`#tempat-area-${rows}`)
  });

  $(`#tanggal-${rows}`).datepicker({
    rtl: KTUtil.isRTL(),
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    // clearBtn: true,
    orientation: "bottom left"
  });

  // $('#produk-' + rows).select2({
  //     placeholder: "Pilih Produk",
  //     dropdownParent: $("#modal_form")
  // });
  $(".kt-selectpicker").selectpicker();
  // protectNumber(`#produk-jumlah-${rows}`, 10);
  getProduk(`#produk-${rows}`, rows, id, tipe, jumlah);
  getArea(`#area-${rows}`);
}

function hapusProduk(id) {
  if (id != "") {
    $("#baris-produk-" + id).remove();
  } else {
    $(".produk_baris").remove();
  }
}

function tambahPallet(id = "", tipe = "", jumlah = "") {
  const tableId = "table_pallet";
  const rows = document.getElementById(tableId).getElementsByTagName("tr")
    .length;

  let html = `<tr class="pallet_baris" id="baris-pallet-${rows}">
                    <td>${rows}</td>
                    <td id="tempat-pallet-${rows}">
                        <select class="form-control m-select2 pilih_pallet" id="pallet-${rows}" name="pallet[]" onchange="checkPallet(this)" aria-placeholder="Pilih Pallet" style="width: 100%;">
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="action_pallet[]" id="pallet-status-${rows}" style="width: 100%;">
                        <option value="1">Mengurangi</option>
                        <option value="2">Menambah</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="pallet_jumlah[]" id="pallet-jumlah-${rows}" class="form-control" placeholder="Masukkan jumlah">
                    </td>
                    <td>
                        <input type="text" name="pallet_alasan[]" id="pallet-alasan-${rows}" class="form-control" placeholder="Masukkan alasan">
                    </td>
                    <td>
                        <button class="btn btn-danger btn-elevate btn-icon btn-sm" onclick="hapusPallet(${rows})"><i class="la la-trash"></i></button>
                    </td>
                </tr>`;
  $("#table_pallet tbody").append(html);
  $(".m-select2").select2({
    placeholder: "Pilih Pallet",
    dropdownParent: $(`#tempat-pallet-${rows}`)
  });
  $(".kt-selectpicker").selectpicker();
  protectNumber(`#pallet-jumlah-${rows}`, 10);
  getPallet(`#pallet-${rows}`, rows, id, tipe, jumlah);
}

function hapusPallet(id) {
  if (id != "") {
    $("#baris-pallet-" + id).remove();
  } else {
    $(".pallet_baris").remove();
  }
}

function getProduk(target, no, id = "", tipe = "", jumlah = "") {
  // $("#table_produk").children('tr:nth-child(' + no + ')').html(`<lines class="shine"></lines>`);
  $.ajax({
    url: ajaxSource + "/" + "get-produk",
    beforeSend: () => {},
    success: res => {
      const obj = res.data;

      let html = `<option value="">Pilih Produk</option>`;
      obj.forEach((item, index) => {
        html += `<option value="${item.id}">${item.nama}</option>`;
      });

      $(target).html(html);
      $(target).val(id);
      $("#produk-status-" + no).val(tipe);
      $("#produk-jumlah-" + no).val(jumlah);
    },
    error: () => {}
  });
}

function getArea(target) {
  // $("#table_produk").children('tr:nth-child(' + no + ')').html(`<lines class="shine"></lines>`);
  $.ajax({
    url: ajaxSource + "/get-area/"+id_gudang,
    beforeSend: () => {},
    success: res => {
      const obj = res.data;
      let html = `<option value="">Pilih Area</option>`;
      obj.forEach((item, index) => {
        html += `<option value="${item.id}">${item.nama}</option>`;
      });

      $(target).html(html);
    },
    error: () => {}
  });
}

function getPallet(target, no, id = "", tipe = "", jumlah = "") {
  $.ajax({
    url: ajaxSource + "/" + "get-pallet",
    success: res => {
      const obj = res.data;

      let html = `<option value="">Pilih Pallet</option>`;
      obj.forEach((item, index) => {
        html += `<option value="${item.id}">${item.nama}</option>`;
      });

      $(target).html(html);
      $(target).val(id);
      $("#pallet-status-" + no).val(tipe);
      $("#pallet-jumlah-" + no).val(jumlah);
    },
    error: () => {}
  });
}

function checkProduk(target) {
  let lokasi = $(".pilih_produk");
  let chosen = false;

  const id_target = $(target)
    .attr("id")
    .replace("-", "");
  for (let i = 0; i < lokasi.length; i++) {
    if (
      $(target).val() != "" &&
      $(target).attr("id") != $(lokasi[i]).attr("id") &&
      $(target).val() == $(lokasi[i]).val()
    ) {
      chosen = true;
    }
  }

  if (chosen == true) {
    $(target)
      .val("")
      .trigger("change.select2");
    swal.fire(
      "Pemberitahuan",
      "Produk sudah dipilih. Silahkan Pilih produk lain!",
      "error"
    );
    $("#produk-" + id_target).val("");
  }
}

function checkPallet(target) {
  let lokasi = $(".pilih_pallet");
  let chosen = false;

  const id_target = $(target)
    .attr("id")
    .replace("-", "");
  for (let i = 0; i < lokasi.length; i++) {
    if (
      $(target).val() != "" &&
      $(target).attr("id") != $(lokasi[i]).attr("id") &&
      $(target).val() == $(lokasi[i]).val()
    ) {
      chosen = true;
    }
  }
  if (chosen == true) {
    $(target)
      .val("")
      .trigger("change.select2");
    swal.fire(
      "Pemberitahuan",
      "Pallet sudah dipilih. Silahkan Pilih pallet lain!",
      "error"
    );
    $("#pallet-" + id_target).val("");
  }
}

function edit(id = "") {
  reset_form();
  $("#id").val(id);
  $("#action").val("edit");
  $("#btn_save").html("Simpan Data");
  $("#modal_form .modal-title").html("Edit Data Kategori Alat Berat");
  $("#modal_form .modal-info").html(
    "Isilah form dibawah ini untuk mengubah data master Kategori Alat Berat sesuai kebutuhan."
  );
  $("#modal_form").modal(
    {
      backdrop: "static",
      keyboard: false
    },
    "show"
  );

  $.ajax({
    type: "GET",
    url: ajaxUrl + "/stock-adjustment/" + id_gudang + "/" + id,
    beforeSend: function() {
      preventLeaving();
      $(".btn_close_modal").addClass("hide");
      $(".se-pre-con").show();
    },
    success: function(response) {
      window.onbeforeunload = false;
      $(".btn_close_modal").removeClass("hide");
      $(".se-pre-con").hide();

      let obj_adjustment = response.data.material_adjustment;
      let obj_produk = response.data.produk;
      let obj_pallet = response.data.pallet;

      if (obj_adjustment.tanggal != null) {
        $("#tanggal").val(helpDateFormat(obj_adjustment.tanggal, "si"));
      }

      if (obj_adjustment.foto != null) {
        let html =
          '<a target="_blank" href="' +
          baseUrl +
          /watch/ +
          obj_adjustment.foto +
          "?" +
          "un=" +
          obj_adjustment.id +
          "&ctg=material&src=" +
          obj_adjustment.foto +
          '">' +
          obj_adjustment.foto +
          "</a>";
        $("#list").html(html);
      }

      obj_produk.forEach(element => {
        tambahProduk(element.id, element.tipe, element.jumlah);
      });

      obj_pallet.forEach(element => {
        tambahPallet(element.id, element.tipe, element.jumlah);
      });
    },
    error: function(response) {
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

function detail(id) {
    $("#modal_detail").modal(
    {
      backdrop: "static",
      keyboard: false
    },
    "show"
  );

  $.ajax({
    type: "GET",
    url: ajaxUrl + "/stock-adjustment/" + id_gudang + "/" + id,
    beforeSend: function () {
      preventLeaving();
      $(".btn_close_modal").addClass("hide");
      $(".se-pre-con").show();
    },
    success: function (response) {
      window.onbeforeunload = false;
      $(".btn_close_modal").removeClass("hide");
      $(".se-pre-con").hide();

      let obj_adjustment = response.data.material_adjustment;
      let obj_produk = response.data.produk;
      let obj_pallet = response.data.pallet;

      if (obj_adjustment.tanggal != null) {
        $("#tempat_tanggal").html(helpDateFormat(obj_adjustment.tanggal, "li"));
      }

      if (obj_adjustment.shift != null) {
        $("#tempat_shift").html(obj_adjustment.shift);
      }

      if (obj_adjustment.foto != null) {
        let html =
          '<a id="gambar" target="_blank" href="' +
          baseUrl +
          /watch/ +
          obj_adjustment.foto +
          "?" +
          "un=" +
          obj_adjustment.id +
          "&ctg=material&src=" +
          obj_adjustment.foto +
          '">' +
          obj_adjustment.foto +
          "</a>";

          const link = baseUrl + /watch/ + obj_adjustment.foto + "?" + "un=" + obj_adjustment.id + "&ctg=material&src=" + obj_adjustment.foto

          $("#tempat_link_gambar").prop("href", link);
          $("#tempat_muncul_gambar").prop("src", link);

        $("#list").html(html);
      } else {
        $("#tempat_link_gambar").prop("href", "");
        $("#tempat_muncul_gambar").prop("src", "");
        $("#list").html("Tidak ada gambar");
      }

        let text = "";
        let i=1;
        obj_produk.forEach(element => {
            let text_tipe = "";
            if (element.tipe == 1) {
                text_tipe = "Mengurangi";
            } else if (element.tipe == 2) {
                text_tipe = "Menambah";
            }

            let text_jenis_produk = "";
            if (element.jenis_produk == 1) {
                text_jenis_produk = "Normal";
            } else if (element.jenis_produk == 2) {
                text_jenis_produk = "Rusak";
            }

            text += `
                <tr>
                    <td>${i}</td>
                    <td>${element.nama}</td>
                    <td>${text_jenis_produk}</td>
                    <td>${element.nama_area}</td>
                    <td>${helpDateFormat(element.tanggal, "li")}</td>
                    <td>${text_tipe}</td>
                    <td>${element.jumlah} Ton</td>
                    <td>${element.alasan ? element.alasan : ''}</td>
                </tr>
            `;
            i++;
        });
        $("#tubuh_produk").html(text);
        text = "";
        i=1;
        obj_pallet.forEach(element => {
            let text_tipe = "";
            if (element.tipe == 1) {
                text_tipe = "Mengurangi";
            } else if (element.tipe == 2) {
                text_tipe = "Menambah";
            }
            text += `
                <tr>
                    <td>${i}</td>
                    <td>${element.nama}</td>
                    <td>${text_tipe}</td>
                    <td>${element.jumlah} pcs</td>
                    <td>${element.alasan ? element.alasan:''}</td>
                </tr>
            `;
            i++;
        });
        $("#tubuh_pallet").html(text);
        
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

function simpan() {
  if (myDropzone.getQueuedFiles().length == 1) { 
    let data = $("#form1").serializeArray();

    $.ajax({
      type: "PUT",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      },
      url: ajaxUrl + "/stock-adjustment/" + id_gudang,
      data: data,
      beforeSend: function() {
        preventLeaving();
        $(".btn_close_modal").addClass("hide");
        $(".se-pre-con").show();
      },
      success: function(response) {
        laddaButton.stop();
        window.onbeforeunload = false;
        $(".btn_close_modal").removeClass("hide");
        $(".se-pre-con").hide();
        let obj = response;
        myDropzone.on("sending", function(file, xhr, formData) {
          formData.append("id", obj.data.id);
        });

        myDropzone.processQueue();

        if (obj.status == "OK") {
          swal.fire("Ok", "Data berhasil disimpan", "success").then(()=>{
            datatable.api().ajax.reload();
            $("#modal_form").modal("hide");
          }).catch(()=>{

          });
        } else {
          swal.fire("Pemberitahuan", obj.message, "warning");
        }
      },
      error: function(response) {
        $("#btn_save").prop("disabled", false);
        let head = "Maaf",
          message = "Terjadi kesalahan koneksi",
          type = "error";
        laddaButton.stop();
        window.onbeforeunload = false;
        $(".btn_close_modal").removeClass("hide");
        $(".se-pre-con").hide();
        if (response["status"] == 401 || response["status"] == 419) {
          location.reload();
        } else {
          if (response["status"] != 404 && response["status"] != 500) {
            let obj = JSON.parse(response["responseText"]);

            if (!$.isEmptyObject(obj.message)) {
              if (obj.code > 450) {
                head = "Maaf";
                message = obj.message;
                type = "error";
              } else {
                head = "Pemberitahuan";
                type = "warning";
                if (!$.isEmptyObject(response.responseJSON.errors)) {
                  obj = response.responseJSON.errors;
                  laddaButton.stop();
                  window.onbeforeunload = false;
                  $(".btn_close_modal").removeClass("hide");
                  $(".se-pre-con").hide();
    
                  const temp = Object.values(obj);
                  message = "";
                  temp.forEach(element => {
                    element.forEach(row => {
                      message += row + "<br>";
                    });
                  });
                } else {
                  message = obj.message
                }
              }
            }
          }

          swal.fire(head, message, type);
        }
      }
    });
  } else {
    swal.fire("Pemberitahuan", "Wajib upload file!", "warning").then(()=>{
        laddaButton.stop();
    }).catch(()=>{
        laddaButton.stop();
    });
  }
}

function tambah() {
  reset_form();
  $("#id").val("");
  $("#action").val("add");
  $("#btn_save").html("Tambah Data");
  $("#modal_form .modal-title").html("Tambah Data Stock Adjustment");
  $("#modal_form .modal-info").html(
    "Isilah form dibawah ini untuk menambahkan data terkait stock adjustment."
  );
  $('#triggerTambahFoto').html('Tambah Foto');
  $("#modal_form").modal(
    {
      backdrop: "static",
      keyboard: false
    },
    "show"
  );
}

function reset_form(method = "") {
    $("#id").val("");
    $("#id").change();
    $("#tanggal").val("");
    $("#table_produk tbody").html("");
    $("#table_pallet tbody").html("");
    $("#list").html("");
    myDropzone.removeAllFiles();
}

function openModalTambah(){
  $('#modal').modal();
  $("#titleModal").html("Tambah Pegawai");
  $('#triggerTambahFoto').html('Tambah Foto')
}
function openModalEdit(){
  $('#modal').modal();
  $("#titleModal").html("Edit Pegawai");
  $('#triggerTambahFoto').html('Ubah Foto')
}

var KTDatatablesDataSourceHtml = (function() {
  var dataJSONArray = JSON.parse(
    '[[1,"10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [2, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [3, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"]]'
  );
  var initTable1 = function() {
    var table = $("#kt_table_1");
    // begin first table
    table.DataTable({
      responsive: true,
      data: dataJSONArray,
      columnDefs: [
        {
          className: "text-center",
          targets: -1,
          title: "Actions",
          orderable: false,
          render: function(data, type, full, meta) {
            return `
                        <a href="" data-toggle="modal" data-target="#kt_modal_1">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Detail">
                            <i class="flaticon-edit-1"></i> </button>
                        </a>
                        <a href="" data-toggle="modal" data-target="#kt_modal_1">
                            <button type = "button" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
                            <i class="flaticon-edit-1"></i> </button>
                        </a>
                        <button type = "button" onclick="showme()" class="btn btn-danger btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i class="flaticon-delete"></i> </button>`;
          }
        },
        {
          className: "text-center",
          targets: -2,
          render: function(data, type, full, meta) {
            var image =
              '<a class="fancybox" rel="ligthbox" href="' +
              data +
              '"><img class="img-responsive" width="100px" src="' +
              data +
              '" alt=""></a>';
            return image;
          }
        }
      ],
      drawCallback: function(settings) {
        $('[data-toggle="kt-tooltip"]').tooltip();
        $(".fancybox").fancybox({
          openEffect: "none",

          closeEffect: "none"
        });
      }
    });
  };
  return {
    //main function to initiate the module
    init: function() {
      initTable1();
    }
  };
})();

function showme() {}
// jQuery(document).ready(function () {
//     KTDatatablesDataSourceHtml.init();
// });
