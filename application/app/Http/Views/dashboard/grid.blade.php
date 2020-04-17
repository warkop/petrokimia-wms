<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
<script>
    document.title = "Dashboard | Warehouse Management System";
    WebFont.load({
        google: {
            "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
        },
        active: function () {
            sessionStorage.fonts = true;
        }
    });

    const baseUrl = "{{url('/')}}/"
</script>
@include('layout.header')
<link rel="stylesheet" href="{{aset_extends('css/global.css')}}">
<script type="text/javascript" src="{{aset_extends('plugin/gchart/loader.js')}}"></script>


<style>
.mbox {   
    display: inline-block;
    width: 10px;
    height: 10px;
    margin: 10px 55px 10px 25px;
    padding-left: 4px;
}
.bgimage{
    background-image:url('assets/extends/img/forklift-1.png');
}
</style>

<div class="row row-no-padding row-col-separator-xl" style="background:#fff">
    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-6 pointer nav---gation" onclick="location.href='{{url('/')}}';">
        <div class="kt-widget24">
            <div class="text-center">
                <div class="text-center">
                    <a href="{{url('/')}}">
                        <h4> <span><i class=""></i></span> Halaman Depan</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-6 pointer nav---gation" onclick="location.href='{{url('layout')}}';" style="z-index:10">
        <div class="kt-widget24">
            <div class="text-center">
                <div class="text-center">
                    <a href="{{url('layout')}}">
                        <h4><span><i class=""></i></span>Menu Utama</h4>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="kt-content  kt-grid__item kt-grid__item--fluid">
    <!--Begin::Dashboard 6-->
    <div id="row-chartLine" class="row v-middle-flex-center">
        {{-- <section> --}}
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="kt-portlet">
                    <div class="kt-portlet__head no-border-bottom">
                        <div class="kt-portlet__head-title">
                            <h4 class="kt-portlet__head-text title_sub pt-4">
                                <br>
                                Dashboard WMS
                                </h4>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group pt-4">
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class=" row" style="margin-bottom:3rem">
                            <div class="form-group col-md-3">
                            <input type="text" class="form-control" id="kt_daterangepicker_2" readonly placeholder="Pilih Periode" type="text" />
                            
                            </div>
                            <div class="form-group col-md-3">
                                
                            <select class="form-control input-enter m-select2" id="pilih_shift" name="param" >
                                <option selected value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                                <option value="3">Shift 3</option>

                            </select>
                            </div>
                            <div class="form-group col-md-3">
                                
                            <select class="form-control input-enter m-select2" id="pilih_gudang" name="param" >
                                @foreach ($gudang as $key)
                                    <option value="{{$key->id}}">{{$key->nama}}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group col-md-2">
                                
                            <button type="button" class="btn btn-primary" style="width:100%" onclick="filter()">Filter</button>
                            </div>
                            <div class="form-group col-md-1">
                                
                            <button type="button" class="btn btn-danger btn-icon"><i class="la la-refresh"></i></button>&nbsp;
                            </div>
                        </div>
                        <!--begin::Accordion-->
                        <div class="accordion  accordion-toggle-arrow" id="accordionExample4">
                            <div class="card">
                                <div class="card-header" id="headingOne4">
                                    <div class="card-title" onclick="showAcc1()" style="background-color:#FFC201; color:black">
                                        <h4>Manajemen Kinerja Gudang</h4>
                                    </div>
                                </div>
                                <div class="Acc1" >
                                    <div class="card-body">
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                            <h5>Realisasi Handling Per Jenis Produk Gudang Gresik</h5>
                                            <div id="jenisproduk" style="width:100%; height:500px;"></div>
                                            </div>
                                            <div class="col-md-6">
                                            <h5>Realisasi Handling Per Gudang</h5>
                                            <div id="gudang" style="width:100%; height:500px;"></div>
                                            </div>
                                            
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <h5>Realisasi Tonase Produk Rusak</h5>
                                                <div id="produkrusak" style="width:100%; height:500px;"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Diagram Realisasi Penggunaan Alat Berat Gudang Gresik</h5>
                                                <div id="realisasipenggunaan" style="width:100%; height:500px;"></div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <h5 class="mt4">Stok Palet dan Terplas Per Tanggal 1 Febuary 2020</h5>
                                            <div id="stokpaletbulan" style="height: 500px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo4">
                                    <div class="card-title collapsed" onclick="showAcc2()" style="background-color:#FFC201; color:black;">
                                        <h4>Manajemen Kualitas Pemuatan Produk</h4>
                                    </div>
                                </div>
                                <div class="Acc2" onclick="hideAcc2()">
                                    <div class="card-body">
                                    <div class="mt-4">
                                        <h5>Diagram Perbandingan Kapasitas Muat Buruh VS Realisasi Muat</h5>
                                        <div id="muatan" style="height: 500px;"></div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header" id="headingThree4">
                                    <div class="card-title collapsed" onclick="showAcc3()" style="background-color:#FFC201; color:black">
                                    <h4>Manajemen Alat Berat</h4>
                                    </div>
                                </div>
                                <div class="Acc3" onclick="hideAcc3()">
                                    <div class="card-body">
                                    <div >
                                       
                                        <div class="row">
                                        
                                        <div class="col-md-4 pr-0 bgimage" style="height:500px">
                                            <div >
                                                <div style="text-align: left; margin-top:20%; padding: 0 20% 0; color:black">
                                                    
                                                    <h2>Laporan Keluhan Alat Berat</h2>
                                                    
                                                </div >
                                                    
                                            </div>
                                        </div>
                                        <div class="col-md-8 pl-0"  >
                                            
                                            <div id="keluhanmuatan" style="width: 100%;height:480px;"></div>
                                        </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingFour4">
                                    <div class="card-title collapsed" onclick="showAcc4()" style="background-color:#FFC201; color:black">
                                    <h4>Manajemen Persediaan Produk</h4>
                                    </div>
                                </div>
                                <div class="Acc4" onclick="hideAcc4()">
                                    <div class="card-body">
                                    <div class="mt-4">
                                        <h5 >Perbandingan Produksi VS Pengeluaran</h5>
                                        <div id="produksipengeluaran" style="height: 500px;"></div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

@include('layout.footer')
<script src="{{('assets/extends/js/page/dashboard.js')}}" defer></script>

<script>
$(function() {
    // dataTableKondisiPalet();
    toggle();
});

function toggle() {
    if (window.innerWidth < 800) {
        $('#row-chartLine').removeClass('v-middle-flex-center'); 
    }
    else {
        $('#row-chartLine').addClass('v-middle-flex-center');         
    }    
}
//Chart
data_chart=[
    { periode: '2020-02-02', a: 10, b: 10 , c: 170 ,d: 310, e: 480 ,f: 630},
    { periode: '2020-02-03', a: 170,b: 350, c: 500 ,d: 300, e: 290 ,f: 540},
    { periode: '2020-02-04', a: 170,b: 170 , c: 300 ,d: 400, e: 550 ,f: 470},
    { periode: '2020-02-05', a: 460,b: 10 , c: 300 ,d: 250, e: 620 ,f: 290},
    { periode: '2020-02-06', a: 720 ,b: 650, c: 480 ,d: 340, e: 590 ,f: 310},
    { periode: '2020-02-07', a: 290 ,b: 670, c: 480 ,d: 450, e: 390 ,f: 450}
]
data_chart2=[
    { periode: '2020-02-02', a: 600, b: 400 },
    { periode: '2020-02-03', a: 530,b: 350},
    { periode: '2020-02-04', a: 500,b: 370 },
    { periode: '2020-02-05', a: 800,b: 1000 },
    { periode: '2020-02-06', a: 720 ,b: 650},
    { periode: '2020-02-07', a: 490 ,b: 670},
    { periode: '2020-02-07', a: 390 ,b: 570}
]

</script>

<!-- chart-line -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
    data.addColumn('string', 'Periode');
    data.addColumn('number', 'Produk ZA');
    data.addColumn('number', 'Produk Urea');
    data.addColumn('number', 'Produk SP-36');
    data.addColumn('number', 'Produk Phonska');
    data.addColumn('number', 'Produk Phonska Plus');
    data.addColumn('number', 'Gudang NPX Kebomas');
    data.addRows([
    
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    
   
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 }, title:"TONASE PUPUK" ,titleTextStyle:{bold:true, italic:false} },
    hAxis: { slantedText:true, slantedTextAngle:45, title:"PERIODE", titleTextStyle:{bold:true, italic:false}  },
    pointSize: 3,
    
    chartArea: {
        bottom: 150
        
      },
    
};

var chart = new google.visualization.LineChart(document.getElementById('jenisproduk'));

chart.draw(data, options);
}
</script>

<!-- chart-line -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

var data = new google.visualization.DataTable();
data.addColumn('string', 'Periode');
    data.addColumn('number', 'Gudang ZA');
    data.addColumn('number', 'Gudang Urea 1A');
    data.addColumn('number', 'Gudang PF 1');
    data.addColumn('number', 'Gudang Phonska');
    data.addColumn('number', 'Gudang Urea 1B');
    data.addColumn('number', 'Gudang Multiguna');
    data.addRows([
    [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
]);


var options = {
    colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    legend:{position: 'bottom', maxTextLines:4},
    vAxis: { gridlines: { count: 5 } , title:"TONASE HANDLING", titleTextStyle:{bold:true, italic:false}},
    hAxis: { slantedText:true, slantedTextAngle:45 , title:"PERIODE", titleTextStyle:{bold:true, italic:false}},
    pointSize: 3,
    chartArea: {
        bottom: 150
      },
};

var chart = new google.visualization.LineChart(document.getElementById('gudang'));

chart.draw(data, options);
}
</script>

<!-- chart-bar -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(getTonaseProdukRusak);

function getTonaseProdukRusak() {

    // var data = new google.visualization.DataTable();
    //     data.addColumn('string', 'Periode');
    //     data.addColumn('number', 'Gudang ZA');
    //     data.addColumn('number', 'Gudang Urea 1A');
    //     data.addColumn('number', 'Gudang PF 1');
    //     data.addColumn('number', 'Gudang Phonska');
    //     data.addColumn('number', 'Gudang Urea 1B');
    //     data.addColumn('number', 'Gudang Multiguna');
    //     data.addRows([
        
    //     [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
    //     [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    //     [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    //     [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
    //     [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    //     [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
        
    
    // ]);


    // var options = {
    //     colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    //     legend:{position: 'bottom', maxTextLines:4},
    //     vAxis: { gridlines: { count: 5 }, title:"PERIODE", titleTextStyle:{bold:true, italic:false} },
    //     hAxis: {  title:"TONASE", titleTextStyle:{bold:true, italic:false} },
    //     chartArea: {
    //         bottom: 150
    //     },
        
    // };

    // var chart = new google.visualization.BarChart(document.getElementById('produkrusak'));

    // chart.draw(data, options);
    const shift = $("#pilih_shift").val();
    const gudang = $("#pilih_gudang").val();
    const tanggal = $("#kt_daterangepicker_2").val();
    $.ajax({
        url:baseUrl+"dashboard/get-tonase-produk-rusak",
        method:"GET",
        data:{
            shift: shift,
            gudang: gudang,
            tanggal: tanggal
        },
        success:(res)=>{
            var data = new google.visualization.DataTable();
            const gudang = res.data[1];
            data.addColumn('string', 'Periode');
            for (let i=0; i<gudang.length; i++) {
                data.addColumn('number', gudang[i].nama);
            }
            data.addRows(res.data[0]);

            var options = {
                colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
                legend:{position: 'bottom', maxTextLines:4},
                vAxis: { gridlines: { count: 5 }, title:"PERIODE", titleTextStyle:{bold:true, italic:false} },
                hAxis: {  title:"TONASE", titleTextStyle:{bold:true, italic:false} },
                chartArea: {
                    bottom: 150
                },
                
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('produkrusak'));

            chart.draw(data, options);
        },
        error:()=>{

        }
    })
}
</script>

<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(getTonaseAlatBerat);

function getTonaseAlatBerat() {

    // var data = new google.visualization.DataTable();
    // data.addColumn('string', 'Periode');
    // data.addColumn('number', 'Gudang ZA');
    // data.addColumn('number', 'Gudang Urea 1A');
    // data.addColumn('number', 'Gudang PF 1');
    // data.addColumn('number', 'Gudang Phonska');
    // data.addColumn('number', 'Gudang Urea 1B');
    // data.addColumn('number', 'Gudang Multiguna');
    // data.addRows([
    //     [ '2020-02-02',  80,  90 ,  170 , 310,  480 , 630],
    //     [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
    //     [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
    //     [ '2020-02-05',  460, 90 ,  300 , 250,  620 , 290],
    //     [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
    //     [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450],
    // ]);

    // console.log(data)

    // var options = {
    //     colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
    //     legend:{position: 'bottom'},
    //     vAxis: { gridlines: { count: 5 } , title:"TONASE ALAT BERAT", titleTextStyle:{bold:true, italic:false}},
    //     hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' , title:"GUDANG", titleTextStyle:{bold:true, italic:false}},
    //     chartArea: {
    //         bottom: 150
    //     },
    // };

    // var chart = new google.visualization.ColumnChart(document.getElementById('realisasipenggunaan'));

    // chart.draw(data, options);
    const shift = $("#pilih_shift").val();
    const gudang = $("#pilih_gudang").val();
    const tanggal = $("#kt_daterangepicker_2").val();
    $.ajax({
        url:baseUrl+"dashboard/get-tonase-alat-berat",
        method:"GET",
        data:{
            shift: shift,
            gudang: gudang,
            tanggal: tanggal
        },
        success:(res)=>{
            var data = new google.visualization.DataTable();
            const gudang = res.data[1];
            data.addColumn('string', 'Periode');
            // if (gudang.isArray()) {
                for (let i=0; i<gudang.length; i++) {
                    data.addColumn('number', gudang[i].nama);
                }
                // gudang.forEach(element => {
                // });
            // } else {
                // data.addColumn('number', gudang.nama);
            // }
            // data.addColumn('number', 'Gudang Urea 1A');
            // data.addColumn('number', 'Gudang PF 1');
            // data.addColumn('number', 'Gudang Phonska');
            // data.addColumn('number', 'Gudang Urea 1B');
            // data.addColumn('number', 'Gudang Multiguna');
            data.addRows(res.data[0]);

            var options = {
                colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
                legend:{position: 'bottom'},
                vAxis: { gridlines: { count: 5 } , title:"TONASE ALAT BERAT", titleTextStyle:{bold:true, italic:false}},
                hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' , title:"GUDANG", titleTextStyle:{bold:true, italic:false}},
                chartArea: {
                    bottom: 150
                },
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('realisasipenggunaan'));

            chart.draw(data, options);
        },
        error:()=>{

        }
    })
}
</script>

<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(getProduksiPengeluaran);

function getProduksiPengeluaran() {
    // var data = new google.visualization.DataTable();
    //     data.addColumn('string', 'Jumlah');
    //     data.addColumn('number', 'Produksi');
    //     data.addColumn('number', 'Pengeluaran');
    //     data.addRows([
        
    //     ['2020-02-02',  600, 400 ],
    //     ['2020-02-03',  530, 350],
    //     ['2020-02-04',  500, 370 ],
    //     ['2020-02-05',  800, 1000 ],
    //     ['2020-02-06',  720 , 650],
    //     ['2020-02-07',  490 , 670],
    //     ['2020-02-07',  390 , 570],
    // ]);

    // var options = {
    //     colors: ['#FFC201','#28DAC6'],
    //     legend:{position: 'bottom'},
    //     vAxis: { gridlines: { count: 5 } , title:"TONASE", titleTextStyle:{bold:true, italic:false}},
    //     hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' },
    //     chartArea: {
    //         bottom: 150
    //     },
    // };

    // var chart = new google.visualization.ColumnChart(document.getElementById('produksipengeluaran'));

    // chart.draw(data, options);

    const shift = $("#pilih_shift").val();
    const gudang = $("#pilih_gudang").val();
    const tanggal = $("#kt_daterangepicker_2").val();

    $.ajax({
        url:baseUrl+"dashboard/get-produksi-pengeluaran",
        method:"GET",
        data:{
            shift: shift,
            gudang: gudang,
            tanggal: tanggal
        },
        success:(res)=>{
            
            var data = new google.visualization.DataTable();
                data.addColumn('string', 'Jumlah');
                data.addColumn('number', 'Produksi');
                data.addColumn('number', 'Pengeluaran');
                data.addRows(res.data);

            var options = {
                colors: ['#FFC201','#28DAC6'],
                legend:{position: 'bottom'},
                vAxis: { gridlines: { count: 5 } , title:"TONASE", titleTextStyle:{bold:true, italic:false}},
                hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' },
                chartArea: {
                    bottom: 150
                },
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('produksipengeluaran'));

            chart.draw(data, options);
        },
        error:()=>{

        }
    })
}
</script>

<!-- chart-column -->
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(getPemuatanProduk);

function getPemuatanProduk() {

    // var data = new google.visualization.DataTable();
    //     data.addColumn('string', 'Jumlah');
    //     data.addColumn('number', 'Kapasitas Muat Buruh');
    //     data.addColumn('number', 'Realisasi Muat');
    //     data.addRows([
        
    //     ['2020-02-02',  600, 400 ],
    //     ['2020-02-03',  530, 350],
    //     ['2020-02-04',  500, 370 ],
    //     ['2020-02-05',  800, 1000 ],
    //     ['2020-02-06',  720 , 650],
    //     ['2020-02-07',  490 , 670],
    //     ['2020-02-07',  390 , 570],
        
    
    // ]);


    // var options = {
    //     colors: ['#FD7F0C','#1ACA98'],
    //     legend:{position: 'bottom'},
    //     vAxis: { gridlines: { count: 5 }, title:"TONASE", titleTextStyle:{bold:true, italic:false} },
    //     hAxis: { slantedText:true, slantedTextAngle:45 },
    //     chartArea: {
    //         bottom: 150
    //     },
    // };

    // var chart = new google.visualization.ColumnChart(document.getElementById('muatan'));

    // chart.draw(data, options);
    const shift = $("#pilih_shift").val();
    const gudang = $("#pilih_gudang").val();
    const tanggal = $("#kt_daterangepicker_2").val();
    
    $.ajax({
        url:baseUrl+"dashboard/get-pemuatan-produk",
        method:"GET",
        data:{
            shift: shift,
            gudang: gudang,
            tanggal: tanggal
        },
        success:(res)=>{
            var data = new google.visualization.DataTable();
                data.addColumn('string', 'Jumlah');
                data.addColumn('number', 'Kapasitas Muat Buruh');
                data.addColumn('number', 'Realisasi Muat');
                data.addRows(res.data);


            var options = {
                colors: ['#FD7F0C','#1ACA98'],
                legend:{position: 'bottom'},
                vAxis: { gridlines: { count: 5 }, title:"TONASE", titleTextStyle:{bold:true, italic:false} },
                hAxis: { slantedText:true, slantedTextAngle:45 },
                chartArea: {
                    bottom: 150
                },
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('muatan'));

            chart.draw(data, options);
        },
        error:()=>{

        }
    })
}
</script>

   <!-- chart-column -->
<script>
// google.charts.load('current', {'packages':['corechart']});
// google.charts.setOnLoadCallback(drawChart);

// function drawChart() {

//     var data = new google.visualization.DataTable();
//         data.addColumn('string', 'Periode');
//         data.addColumn('number', 'Gudang ZA');
//         data.addColumn('number', 'Gudang Urea 1A');
//         data.addColumn('number', 'Gudang PF 1');
//         data.addColumn('number', 'Gudang Phonska');
//         data.addColumn('number', 'Gudang Urea 1B');
//         data.addColumn('number', 'Gudang Multiguna');
//         data.addRows([
        
//         [ '2020-02-02',  10,  10 ,  170 , 310,  480 , 630],
//         [ '2020-02-03',  170, 350,  500 , 300,  290 , 540],
//         [ '2020-02-04',  170,170 ,  300 , 400,  550 , 470],
//         [ '2020-02-05',  460, 10 ,  300 , 250,  620 , 290],
//         [ '2020-02-06',  720 , 650,  480 , 340,  590 , 310],
//         [ '2020-02-07',  290 , 670,  480 , 450,  390 , 450]
        
    
//     ]);


//     var options = {
//         colors: ['#FD7F0C','#FFC201','#38DCCA','#007CFF','#00AF4C','#5767DE'],
//         legend:{position: 'bottom', maxTextLines:4},
//         vAxis: { gridlines: { count: 5 } },
//         hAxis: { slantedText:true, slantedTextAngle:45 }
//     };

//     var chart = new google.visualization.ColumnChart(document.getElementById('realisasialatberat'));

//     chart.draw(data, options);
// }
</script>

<!-- chart-pie -->
<script>
google.charts.load('current', {'packages':['corechart']});
// google.charts.setOnLoadCallback(getKeluhanAlatBerat);

// function getKeluhanAlatBerat() {

//     var data = google.visualization.arrayToDataTable([
//         ['Laporan', 'Jumlah'],
//         ['Ban bocor',     18],
//         ['Kedisiplinan Operator',      7],
//         ['Kantong produk rusak',  8],
//         ['Staple roboh',     5],
//         ['Terplas rusak',     13],
//         ['Rem rusak',      5],
//         ['Oli bocor',  7],
//         ['Merusak Pilar Gudang',  8],
    
//     ]);

//     var total = getTotal(data);

//     var yearPattern = "0";
//     var formatNumber = new google.visualization.NumberFormat({
//         pattern: 'decimal', 
//         prefix: 'Rp.'
//     });
//     formatNumber.format(data, 1);

//     var options = {
//         colors: ['#0FA3BA','#FFC201','#5767DE','#FD367B','#FD7F0C','#007CFF','#00AF4C','#28DAC6'],
//         legend:{position: 'right'},
//         height:500,
//         pieSliceText: 'none'
//     };

//     var chart = new google.visualization.PieChart(document.getElementById('keluhanmuatand'));

//     chart.draw(data, options);

//     const shift = $("#pilih_shift").val();
//     const gudang = $("#pilih_gudang").val();
//     const tanggal = $("#kt_daterangepicker_2").val();

//     $.ajax({
//         url:baseUrl+"dashboard/get-keluhan-alat-berat",
//         method:"GET",
//         data:{
//             shift: shift,
//             gudang: gudang,
//             tanggal: tanggal
//         },
//         success:(res)=>{
//             var data = google.visualization.arrayToDataTable(res.data);

//             var total = getTotal(data);

//             var yearPattern = "0";
//             var formatNumber = new google.visualization.NumberFormat({
//                 pattern: 'decimal', 
//                 prefix: 'Rp.'
//             });
//             formatNumber.format(data, 1);

//             var options = {
//                 colors: ['#0FA3BA','#FFC201','#5767DE','#FD367B','#FD7F0C','#007CFF','#00AF4C','#28DAC6'],
//                 legend:{position: 'right'},
//                 height:500,
//                 pieSliceText: 'none'
//             };

//             var chart = new google.visualization.PieChart(document.getElementById('keluhanmuatand'));

//             chart.draw(data, options);
//         },
//         error:()=>{

//         }
//     })
// }

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(getJumlahPallet);

    function getJumlahPallet() {
        // Some raw data (not necessarily accurate)
        // var data = google.visualization.arrayToDataTable([
        //   ['Gudang', 'Pakai & Dasaran', 'Kosong ', 'Rusak', 'Total Stok'],
        //   ['Gudang ZA',  165,      938,         522,             998],
        //   ['Gudang Urea IA',  135,      1120,        599,             1268],
        //   ['Gudang PF I',  157,      1167,        587,             1207],
        //   ['Gudang Phonska',  139,      1010,        615,             1068],
        //   ['Gudang Urea IB',  136,      691,         629,             1026],
        //   ['Gudang Multiguna',  135,      1120,        599,             1268],
        // ]);

        // var options = {
          
        //   colors: ['#FFC201','#28DAC6','#FD7F0C','#00AF4C'],
        //   seriesType: 'bars',
        //   series: {3: {type: 'line'}}  ,
        //   legend:{position: 'bottom'} ,
        //   vAxis: { title:"JUMLAH PALET", titleTextStyle:{bold:true, italic:false}} ,    
        //   hAxis: { title:"GUDANG", titleTextStyle:{bold:true, italic:false}},
        //   chartArea: {
        //         bottom: 150
        //     },
          
        // };

        // var chart = new google.visualization.ComboChart(document.getElementById('stokpaletbulan'));
        // chart.draw(data, options);

        const shift = $("#pilih_shift").val();
        const gudang = $("#pilih_gudang").val();
        const tanggal = $("#kt_daterangepicker_2").val();


        $.ajax({
            url:baseUrl+"dashboard/get-jumlah-pallet",
            method:"GET",
            data:{
                shift: shift,
                gudang: gudang,
                tanggal: tanggal
            },
            success:(res)=>{
                // var dataArray = [
                //     ['Gudang', 'Pakai & Dasaran', 'Kosong ', 'Rusak', 'Total Stok']
                // ];

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Gudang');
                data.addColumn('number', 'Pakai & Dasaran');
                data.addColumn('number', 'Kosong');
                data.addColumn('number', 'Rusak');
                data.addColumn('number', 'Total Stok');
                data.addRows(res.data);

                // data.unshift(['Gudang', 'Pakai & Dasaran', 'Kosong ', 'Rusak', 'Total Stok']);
                // console.log(data);
                var options = {
                
                colors: ['#FFC201','#28DAC6','#FD7F0C','#00AF4C'],
                seriesType: 'bars',
                series: {3: {type: 'line'}}  ,
                legend:{position: 'bottom'} ,
                vAxis: { title:"JUMLAH PALET", titleTextStyle:{bold:true, italic:false}} ,    
                hAxis: { title:"GUDANG", titleTextStyle:{bold:true, italic:false}},
                chartArea: {
                        bottom: 150
                    },
                
                };

                var chart = new google.visualization.ComboChart(document.getElementById('stokpaletbulan'));
                
                chart.draw(data, options);
            },
            error:()=>{

            }
        })
    }
    // Class definition
var KTSelect2 = function() {
    // Private functions
    var demos = function() {
        // basic
        $('#pilih_shift').select2({
            placeholder: "Pilih Shift"
        });
        $('#pilih_gudang').select2({
            placeholder: "Pilih Gudang"
        });       
    }

    

    // Public functions
    return {
        init: function() {
            demos();
            
        }
    };
}();

// Initialization
jQuery(document).ready(function() {
    KTSelect2.init();
});


// Class definition

var KTBootstrapDaterangepicker = function () {
    
    // Private functions
    var demos = function () {
        // minimum setup
        // $('#kt_daterangepicker_1').daterangepicker({
        //     buttonClasses: ' btn',
        //     applyClass: 'btn-primary',
        //     cancelClass: 'btn-secondary'
        // });
        $('#kt_daterangepicker_2').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary'
        }, function(start, end, label) {
            $('#kt_daterangepicker_2 .form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        });

    }
    return {
        // public functions
        init: function() {
            demos(); 
            
        }
    };
}();

jQuery(document).ready(function() {
    KTBootstrapDaterangepicker.init();
});

function showAcc1(){
   $('.Acc1').toggle();
}


function showAcc2(){
   $('.Acc2').toggle();
}


function showAcc3(){
   $('.Acc3').toggle();
}


function showAcc4(){
   $('.Acc4').toggle();
}

// function getProduksiPengeluaran() {
//     const shift = $("#pilih_shift").val();
//     const gudang = $("#pilih_gudang").val();
//     const tanggal = $("#kt_daterangepicker_2").val();

    

//     $.ajax({
//         url:baseUrl+"dashboard/get-produksi-pengeluaran",
//         method:"GET",
//         data:{
//             shift: shift,
//             gudang: gudang,
//             tanggal: tanggal
//         },
//         success:(res)=>{
            
//             var data = new google.visualization.DataTable();
//                 data.addColumn('string', 'Jumlah');
//                 data.addColumn('number', 'Produksi');
//                 data.addColumn('number', 'Pengeluaran');
//                 data.addRows(res.data);

//             var options = {
//                 colors: ['#FFC201','#28DAC6'],
//                 legend:{position: 'bottom'},
//                 vAxis: { gridlines: { count: 5 } , title:"TONASE", titleTextStyle:{bold:true, italic:false}},
//                 hAxis: { slantedText:true, slantedTextAngle:45,format: 'long' },
//                 chartArea: {
//                     bottom: 150
//                 },
//             };

//             var chart = new google.visualization.ColumnChart(document.getElementById('produksipengeluaran'));

//             chart.draw(data, options);

//             // google.charts.setOnLoadCallback(data);
//         },
//         error:()=>{

//         }
//     })
// }

var dataArray = [
    ['Ban bocor',     5],
    ['Kedisiplinan Operator',      7],
    ['Kantong produk rusak',  8],
    ['Staple roboh',     5],
    ['Terplas rusak',     13],
    ['Rem rusak',      5],
    ['Oli bocor',  7],
    ['Merusak Pilar Gudang',  8],
];

function getKeluhanAlatBerat() {
    

    // var dataArray = [
    //     ['Ban bocor',     5],
    //     ['Kedisiplinan Operator',      7],
    //     ['Kantong produk rusak',  8],
    //     ['Staple roboh',     5],
    //     ['Terplas rusak',     13],
    //     ['Rem rusak',      5],
    //     ['Oli bocor',  7],
    //     ['Merusak Pilar Gudang',  8],
    // ];

    // var total = getTotal(dataArray);

    // var yearPattern = "0";
    // var formatNumber = new google.visualization.NumberFormat({
    //     pattern: 'decimal', 
    //     prefix: 'Rp.'
    // });
    // formatNumber.format(data, 1);

    // var options = {
    //     colors: ['#0FA3BA','#FFC201','#5767DE','#FD367B','#FD7F0C','#007CFF','#00AF4C','#28DAC6'],
    //     legend:{position: 'right'},
    //     height:500,
    //     pieSliceText: 'none'
    // };

    // var chart = new google.visualization.PieChart(document.getElementById('keluhanmuatand'));

    // chart.draw(data, options);

    const shift = $("#pilih_shift").val();
    const gudang = $("#pilih_gudang").val();
    const tanggal = $("#kt_daterangepicker_2").val();
        console.log("keluhan")
    $.ajax({
        url:baseUrl+"dashboard/get-keluhan-alat-berat",
        method:"GET",
        data:{
            shift: shift,
            gudang: gudang,
            tanggal: tanggal
        },
        success:(res)=>{
            var dataArray = res.data;
            var total = getTotal(dataArray);

            // Adding tooltip column  
            for (var i = 0; i < dataArray.length; i++) {
                dataArray[i].push(customTooltip(dataArray[i][0], dataArray[i][1], total));
            }

            // Changing legend  
            for (var i = 0; i < dataArray.length; i++) {
                dataArray[i][0] =  dataArray[i][0]  +'  '+((dataArray[i][1] / total) * 100).toFixed(1) + '% (' +  dataArray[i][1] + ') '  ; 
            }

            // Column names
            dataArray.unshift(['Goal Name', 'No. of times Requested', 'Tooltip']);

            var data = google.visualization.arrayToDataTable(dataArray);

            // Setting role tooltip
            data.setColumnProperty(2, 'role', 'tooltip');
            data.setColumnProperty(2, 'html', true);

            var options = {
                //title: 'Most Requested Sponsors',
                
                height: 500,
                tooltip: { isHtml: true },
                colors: ['#0FA3BA','#FFC201','#5767DE','#FD367B','#FD7F0C','#007CFF','#00AF4C','#28DAC6'],
                pieSliceText: 'none'
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('keluhanmuatan'));
            chart.draw(data, options);
        },
        error:()=>{

        }
    })
}

function customTooltip(name, value, total) {
    return name + '<br/><b>' + value + ' (' + ((value/total) * 100).toFixed(1) + '%)</b>';
}

function getTotal(dataArray) {
    var total = 0;
    for (var i = 0; i < dataArray.length; i++) {
        total += dataArray[i][1];
    }
    return total;
}

google.load('visualization', '1', {packages:['corechart'], callback: getKeluhanAlatBerat});

function filter() {
    getKeluhanAlatBerat();
    getJumlahPallet();
    getProduksiPengeluaran()
    getPemuatanProduk()
    getTonaseAlatBerat()
    getTonaseProdukRusak()
}
</script>