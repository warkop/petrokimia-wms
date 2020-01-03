<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\Gudang;
use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RealisasiMaterial;
use App\Http\Models\ShiftKerja;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function laporanAktivitas()
    {
        $data['title'] = 'Laporan Aktivitas';
        return view('report.aktivitas.grid', $data);
    }

    public function aktivitasHarian()
    {
        $tgl_awal   = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = AktivitasHarian::with('aktivitas')->with('gudang')->with('checker')->with('produk')->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_aktivitas_harian.xlsx';
        $this->generateExcelAktivitas($res, $nama_file, $tgl_awal, $tgl_akhir);
    }

    public function laporanKeluhanAlatBerat()
    {
        $data['title'] = 'Laporan Keluhan Alat Berat';
        $data['kategori'] = KategoriAlatBerat::all();
        return view('report.keluhan-alat-berat.grid', $data);
    }

    public function keluhanAlatBerat()
    {
        $jenis_alat_berat   = request()->input('jenis_alat_berat');
        $status_tindak_lanjut  = request()->input('status_tindak_lanjut');

        $res = LaporanKerusakan::with('alatBerat', 'alatBerat.kategori')
        ->with('kerusakan')
        ->with('shift')
        ->with('operator')
        ->with('foto')
        // ->where('id_kategori', $jenis_alat_berat)
        ->whereHas('alatBerat', function ($query) use ($jenis_alat_berat) {
            foreach ($jenis_alat_berat as $key => $value) {
                $query->orWhere('id_kategori', $value);
            }
        })
        ->where('status', $status_tindak_lanjut)
        ->where('jenis', '2')
        ->get();

        // dd($res->toArray());

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_kerusakan_alat_berat.xlsx';
        $this->generateExcelKeluhanAlatBerat($res, $nama_file);
    }

    public function generateExcelAktivitas($res, $nama_file, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Aktivitas Harian');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL '.date('d/m/Y', strtotime($tgl_awal)).' - '.date('d/m/Y', strtotime($tgl_akhir)));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

        

        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA AKTIVITAS');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA GUDANG');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA CHECKER');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PRODUK');


        $style_judul_kolom = array(
            'fill' => array(
                // 'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'font' => array(
                'bold' => true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":F" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // var_dump($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $style_ontop = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $style_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                ),

            );
            
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":F" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->aktivitas->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->checker->nama);
            $col++;
            $temp = '';
            foreach ($value->produk as $key) {
                if ($temp == '') {
                    $temp = $key->nama;
                } else {
                    $temp = $temp.', '.$key->nama;
                }
            }
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $temp);

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Aktivitas Harian");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function generateExcelKeluhanAlatBerat($res, $nama_file)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kerusakan Alat Berat');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        // $row++;
        // $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);



        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Registrasi');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Alat Berat');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Nama Gudang');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Keluhan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Lambung');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Keterangan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dokumentasi');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tindak Lanjut Rekanan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal Tindak Lanjut');


        $style_judul_kolom = array(
            'fill' => array(
                // 'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'font' => array(
                'bold' => true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // var_dump($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $style_ontop = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $style_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                ),

            );

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':I' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->kategori->nama);
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->kerusakan->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->nomor_lambung);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keterangan);
            $col++;

            $temp = '';
            $x = 5;
            $y = 5;
            foreach ($value->foto as $row2) {
                $temp .= $row2->file_enc;
                
                if (!empty($value->id) && file_exists(storage_path("/app/public/history/" . $value->id . "/" . $row2->file_enc))) {
                    $objDrawing = new Drawing;
                    $objDrawing->setName($row2->file_ori);
                    $objDrawing->setDescription('gambar ' . $row2->file_ori);
                    $objDrawing->setPath(storage_path() . "/app/public/history/" . $value->id . "/" . $row2->file_enc);
                    $objDrawing->setCoordinates(strtoupper(toAlpha($col - 1)) . $row);
                    //setOffsetX works properly
                    $objDrawing->setOffsetX($x);
                    $objDrawing->setOffsetY($y);
                    //set width, height
                    // $objDrawing->setWidth(100);
                    $objDrawing->setHeight(110);
                    $objDrawing->setWorksheet($objSpreadsheet->getActiveSheet());
                    $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
                    
                    // $x += 110;
                    $y += $objDrawing->getHeight();
                    $objSpreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($y);
                } else {
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "File tidak ada di server ". $value->id);
                }
            }
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $temp);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->status==0?"Belum":"Sudah");

            $lap = LaporanKerusakan::where('induk', $value->id)->where('status', 1)->orderBy('id', 'desc')->first();
            
            if (!empty($lap)) {
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, helpDate($lap->created_at, 'si'));
            }

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Keluhan Alat Berat");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanProduk()
    {
        $data['title'] = 'Laporan Produk';
        $data['gudang'] = Gudang::all();
        $data['produk'] = Material::produk()->get();
        return view('report.produk.grid', $data);
    }

    public function produk()
    {
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = AreaStok::
        with('area', 'area.gudang')
        ->with('produk')
        ->whereHas('area', function ($query) use ($gudang) {
            foreach ($gudang as $key => $value) {
                $query->orWhere('id_kategori', $value);
            }
        })
        ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
        ->get();
        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_aktivitas_harian.xlsx';
        $this->generateExcelAktivitas($res, $nama_file, $tgl_awal, $tgl_akhir);
    }

    public function laporanMutasiPallet()
    {
        $data['title'] = 'Laporan Pallet';
        $data['gudang'] = Gudang::all();
        $data['pallet'] = Material::pallet()->get();
        return view('report.mutasi-pallet.grid', $data);
    }

    public function generateExcelProduk($res, $nama_file)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Produk');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        // $row++;
        // $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);



        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Registrasi');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Alat Berat');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Nama Gudang');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Keluhan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Lambung');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Keterangan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dokumentasi');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tindak Lanjut Rekanan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal Tindak Lanjut');


        $style_judul_kolom = array(
            'fill' => array(
                // 'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'font' => array(
                'bold' => true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // var_dump($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $style_ontop = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $style_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                ),

            );

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':I' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->kategori->nama);
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->kerusakan->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->nomor_lambung);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keterangan);
            $col++;

            $temp = '';
            $x = 5;
            $y = 5;
            foreach ($value->foto as $row2) {
                $temp .= $row2->file_enc;

                if (!empty($value->id) && file_exists(storage_path("/app/public/history/" . $value->id . "/" . $row2->file_enc))) {
                    $objDrawing = new Drawing;
                    $objDrawing->setName($row2->file_ori);
                    $objDrawing->setDescription('gambar ' . $row2->file_ori);
                    $objDrawing->setPath(storage_path() . "/app/public/history/" . $value->id . "/" . $row2->file_enc);
                    $objDrawing->setCoordinates(strtoupper(toAlpha($col - 1)) . $row);
                    //setOffsetX works properly
                    $objDrawing->setOffsetX($x);
                    $objDrawing->setOffsetY($y);
                    //set width, height
                    // $objDrawing->setWidth(100);
                    $objDrawing->setHeight(110);
                    $objDrawing->setWorksheet($objSpreadsheet->getActiveSheet());
                    $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);

                    // $x += 110;
                    $y += $objDrawing->getHeight();
                    $objSpreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($y);
                } else {
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "File tidak ada di server " . $value->id);
                }
            }
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $temp);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->status == 0 ? "Belum" : "Sudah");

            $lap = LaporanKerusakan::where('induk', $value->id)->where('status', 1)->orderBy('id', 'desc')->first();

            if (!empty($lap)) {
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, helpDate($lap->created_at, 'si'));
            }

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Keluhan Alat Berat");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function mutasiPallet()
    {
        $gudang             = request()->input('gudang'); //multi
        $pallet             = request()->input('pallet');
        $pilih_pallet       = request()->input('pilih_pallet'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = GudangStok::where(function ($query) use ($gudang) {
            foreach ($gudang as $key => $value) {
                $query->where('id_gudang', $value);
            }
        })->get();
        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_mutasi_pallet.xlsx';
        $this->generateExcelMutasiPallet($res, $nama_file, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelMutasiPallet()
    {
        # code...
    }

    public function laporanRealisasi()
    {
        $data['gudang']     = Gudang::all();
        $data['produk']     = Material::lainlain()->get();
        $data['shift']      = ShiftKerja::all();
        $data['aktivitas']  = Aktivitas::all();
        return view('report.realisasi.grid', $data);
    }

    public function realisasi()
    {
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $shift              = request()->input('shift'); //multi
        $kegiatan           = request()->input('kegiatan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));
        

        // $realisasiMaterial = RealisasiMaterial::where(function ($query) use ($shift){
        //     foreach ($shift as $key => $value) {
        //         $query->where('id_shift', $value);
        //     }
        // });

        $res = RealisasiMaterial::select(
            'realisasi_material.id',
            'tipe',
            'm.nama',
            'mt.jumlah',
            'realisasi_material.tanggal',
            'gs.id_gudang',
            'realisasi_material.id_shift',
            'g.nama as nama_gudang'
        )
        ->leftJoin('material_trans as mt', 'realisasi_material.id', '=', 'id_realisasi_material')
        ->leftJoin('material as m', 'm.id', '=', 'id_material')
        ->leftJoin('gudang_stok as gs', 'gs.id_material', '=', 'mt.id_material')
        ->leftJoin('gudang as g', 'g.id', '=', 'gs.id_gudang')
        ->where(function ($query) use ($shift) {
            foreach ($shift as $key => $value) {
                $query->orWhere('id_shift', $value);
            }
        })
        ->where(function ($query) use ($gudang) {
            foreach ($gudang as $key => $value) {
                $query->orWhere('id_gudang', $value);
            }
        })
        ->where(function ($query) use ($pilih_produk,$produk) {
            if ($produk == 2) {
                foreach ($pilih_produk as $key => $value) {
                    $query->orWhere('m.id', $value);
                }
            }
        });

        $res = $res->get();
        // dd($res->toArray());
        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_realisasi.xlsx';
        $this->generateExcelRealisasi($res, $nama_file, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelRealisasi($res, $nama_file)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Realisasi');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        // $row++;
        // $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);



        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Nama Gudang');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Produk');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Shift');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kuantum');

        $style_judul_kolom = array(
            'fill' => array(
                // 'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'font' => array(
                'bold' => true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":F" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // var_dump($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $style_ontop = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $style_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                ),

            );

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":F" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d/m/Y', strtotime($value->tanggal)));
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->id_shift);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->status == 0 ? "Belum" : "Sudah");

            // $lap = LaporanKerusakan::where('induk', $value->id)->where('status', 1)->orderBy('id', 'desc')->first();

            // if (!empty($lap)) {
            //     $col++;
            //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, helpDate($lap->created_at, 'si'));
            // }

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Realisasi");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanKeluhanGp()
    {
        
    }

    public function keluhanGp()
    {

    }

    public function generateExcelKeluhanGp()
    {
        
    }

    public function laporanMaterial()
    {
        $data['gudang'] = Gudang::all();
        $data['pallet'] = Material::pallet()->get();
        return view('report.material.grid', $data);
    }

    public function material()
    {
        $gudang             = request()->input('gudang'); //multi
        $pallet             = request()->input('pallet');
        $pilih_pallet       = request()->input('pilih_pallet'); //multi
        $tgl_awal           = request()->input('tgl_awal') == null? '' : date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = request()->input('tgl_awal') == null ? '' : date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        // $res = DB::table('gudang as g')->select(
        //     'g.nama as nama_gudang',
        //     'm.nama as nama_material',
        //     'ars.jumlah',
        //     DB::raw('(SELECT jumlah FROM material_trans WHERE id_area_stok = ars.id ORDER BY id asc limit 1) as stok_awal')
        // )
        // ->join(DB::raw('area as a'), 'a.id_gudang', '=', 'g.id')
        // ->join(DB::raw('material as m'), 'm.id', '=', 'a.id_gudang')
        // ->join(DB::raw('area_stok as ars'), 'm.id', '=', 'ars.id_material')
        // ->where(function ($query) use ($gudang) {
        //     foreach ($gudang as $key => $value) {
        //         $query = $query->orWhere('g.id_gudang', $value);
        //     }
        // })
        // ->where(function ($query) use ($produk, $pilih_produk) {
        //     if ($produk == 2) {
        //         foreach ($pilih_produk as $key => $value) {
        //             $query->orWhere('m.id', $value);
        //         }
        //     }
        // })
        // ->get()
        // ;

        // dd($gudang);

        $res = MaterialTrans::distinct()->select(
            'id_material',
            'id_gudang_stok',
            'id_aktivitas_harian'
        )
        ->with('material')
        ->with('gudangStok', 'gudangStok.gudang')
        ->with('aktivitasHarian')
        ->whereHas('gudangStok', function ($query) use ($gudang) {
            foreach ($gudang as $key => $value) {
                $query = $query->orWhere('id_gudang', $value);
            }
        })
        ;

        if ($pallet == 2) {
            $res = $res->whereHas('material', function ($query) use ($pilih_pallet) {
                foreach ($pilih_pallet as $key => $value) {
                    $query = $query->orWhere('id', $value);
                }
            });
        } else {
            $res = $res->whereHas('material', function ($query) {
                $query = $query->where('kategori', 2);
            });
        }

        $res = $res->get();

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_material.xlsx';
        $this->generateExcelMaterial($res, $nama_file, $tgl_awal, $tgl_akhir);

        // dd($res->toArray());
    }

    public function generateExcelMaterial($res, $nama_file)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Material');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        // $row++;
        // $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);



        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Material');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');
        $objSpreadsheet->getActiveSheet()->mergeCells('E' . $row . ':G' . $row);
        // foreach ($variable as $key => $value) {
        //     # code...
        // }

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pengeluaran');
        // foreach ($variable as $key => $value) {
        //     # code...
        // }


        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');

        $style_judul_kolom = array(
            'fill' => array(
                // 'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'font' => array(
                'bold' => true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // var_dump($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $style_ontop = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $style_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                ),

            );

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":I" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':I' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            if ($value->gudangStok != null) {
                if ($value->gudangStok->gudang != null) {
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudangStok->gudang->nama);
                }
            } else {
            }

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

            $col++;

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Keluhan Alat Berat");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }
}
