<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasHarian;
use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\LaporanKerusakan;
use App\Http\Resources\ReportAktivitasHarianResource;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
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
            $query->where('id_kategori', $jenis_alat_berat);
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
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
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
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Registrasi');
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

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":J" . $row)->applyFromArray($style_judul_kolom);
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

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":J" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
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
            foreach ($value->foto as $row2) {
                $temp .= $row2->file_enc;
            }
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $temp);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->status==0?"Belum":"Sudah");

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
}
