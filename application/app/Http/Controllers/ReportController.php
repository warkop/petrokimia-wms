<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\Keluhan;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RealisasiMaterial;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use App\Http\Models\Yayasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        $res = AktivitasHarian::with('aktivitas')
        ->with('gudang')
        ->with('materialTrans.material')
        ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
        ->where('draft', 0)
        ->whereHas('aktivitas', function($query) {
            $query->whereNull('peminjaman');
        })
        ->whereHas('materialTrans.material', function($query) {
            $query->where('kategori', 1);
        })
        ->get();
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
        ->has('kerusakan')
        ->whereHas('alatBerat', function ($query) use ($jenis_alat_berat) {
            $query->where('id_kategori', $jenis_alat_berat[0]);
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

            $users = Users::find($value->created_by);
            $tkbm = TenagaKerjaNonOrganik::find($users->id_tkbm);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tkbm->nama);

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
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "File tidak ada di server ");
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
        $validator = Validator::make(
            request()->all(),
            [
                'tgl_awal' => 'required',
                'tgl_akhir' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'tgl_awal' => 'Tanggal Awal',
                'tgl_akhir' => 'Tanggal Akhir',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-produk')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = AreaStok::distinct()->select(
            'id_material',
            'id_area'
        )
        ->with('material')
        ->with('area', 'area.gudang');

        if ($gudang) {
            $res = $res->whereHas('area.gudang', function ($query) use ($gudang) {
                $query = $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id_gudang', $value);
                }
            });
        }

        if ($produk == 2) {
            $res = $res->where(function ($query) use ($pilih_produk) {
                $query = $query->where('id_material', $pilih_produk[0]);
                foreach ($pilih_produk as $key => $value) {
                    $query = $query->orWhere('id_material', $value);
                }
            });
        } else {
            $res = $res->whereHas('material', function ($query) {
                $query = $query->where('kategori', 1);
            });
        }

        $res = $res->orderBy('id_material')->get();

        $nama_file = date("YmdHis") . '_produk.xlsx';
        $this->generateExcelProduk($res, $nama_file, $tgl_awal, $tgl_akhir);
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
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Material');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

        $gudang = Gudang::all();
        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
        }
        $row = 5;

        $abjadPemasukan = chr(ord($abjadPemasukan) - 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadPemasukan . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pengeluaran');
        
        
        $i = 0;
        $row = 6;
        $abjadPengeluaran = $abjadPemasukan;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPengeluaran++;
        }
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row-1) . ':' . $abjadPengeluaran . ($row-1));
        
        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Rusak');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Siap Jual');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $abjad = 'A';
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
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . ($row + 1))->applyFromArray($style_judul_kolom);
        $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // dd($res->toArray());
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

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':' . $abjadPengeluaran . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->area->gudang->nama);

            $material_trans = MaterialTrans::where('id_material', $value->id_material)
                // ->whereNotNull('id_adjustment')
                ->orderBy('id', 'asc')
                ->first();

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);

            $col++;
            if (!empty($material_trans)) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $material_trans->jumlah);
                $stokAkhir = $material_trans->jumlah;
            } else {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 0);
                $stokAkhir = 0;
            }

            // $aktivitasHarian = AktivitasHarian::where('id_gudang_tujuan', $value->area->id_gudang)
            // ->orWhere('id_gudang', $value->area->id_gudang)->get();
            // dd($aktivitasHarian->toArray());

            
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                ->where('tipe', 2)
                ->where('id_material', $value->id_material)
                ->sum('jumlah');
                // dd($materialTrans);
                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }

            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                    ->where('tipe', 1)
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir);

            $rusak = 0;
            $materialTrans = MaterialTrans::
                where('tipe', 1)
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->sum('jumlah');

            if ($materialTrans) {
                $rusak += $materialTrans;
            }
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $rusak);

            $siapJual = $stokAkhir-$rusak;
            
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $siapJual);

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Produk");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanMutasiPallet()
    {
        $data['title'] = 'Laporan Pallet';
        $data['gudang'] = Gudang::internal()->get();
        $data['pallet'] = Material::pallet()->get();
        return view('report.mutasi-pallet.grid', $data);
    }

    public function mutasiPallet()
    {
        $gudang             = request()->input('gudang'); //multi
        $pallet             = request()->input('pallet');
        $pilih_pallet       = request()->input('pilih_pallet'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = new GudangStok;
        $res = $res->with('gudang')->whereHas('gudang', function($query) {
            $query->where('tipe_gudang', 1);
        });


        $resGudang = Gudang::internal()->get();
        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });

            $resGudang = Gudang::where(function ($query) use ($gudang){
                $query->where('id', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id', $value);
                }
            })->get();
        }

        if ($pallet == 2) {
            $res = $res->where(function ($query) use ($pilih_pallet) {
                foreach ($pilih_pallet as $key => $value) {
                    $query = $query->orWhere('id_material', $value);
                }
            });
        } else {
            $res = $res->whereHas('material', function ($query) {
                $query = $query->where('kategori', 2);
            });
        }

        $res = $res
        // ->where('created_at', '<=', date('Y-m-d', strtotime($tgl_awal)))
        ->orderBy('id_gudang', 'asc')->get()->groupBy('id_material');

        $nama_file = date("YmdHis") . '_mutasi_pallet.xlsx';
        // dd($res->toArray());
        $this->generateExcelMutasiPallet($res, $nama_file, $resGudang, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelMutasiPallet($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
        $style_judul_kolom = array(
            'fill' => array(
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
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_isi_kolom = array(

            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            )
        );
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
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        //end: style

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Mutasi Pallet (Bulan)');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir)));
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Pallet');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kondisi Pallet');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
        }
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pemasukan
        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) - 1);
        $col++;
        $abjadPemasukan++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadPemasukan . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pengeluaran');


        $i = 0;
        $row = 6;
        $abjadPengeluaran = $abjadPemasukan;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPengeluaran++;
        }
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pengeluaran
        $col++;
        $abjadPengeluaran++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row - 1) . ':' . $abjadPengeluaran . ($row - 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row - 1), 'Penyusutan');

        $i = 0;
        $row = 6;
        $abjadPemasukan = $abjadPengeluaran;
        $yayasan = Yayasan::all();
        foreach ($yayasan as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPengeluaran++;
        }
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pengeluaran
        $col++;
        $abjadPengeluaran++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row - 1) . ':' . $abjadPengeluaran . ($row - 1));

        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dipinjam');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'Peminjam');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . $row);

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dikembalikan');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row + 1), 'Peminjam');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . $row);

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peralihan Kondisi Bertambah');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peralihan Kondisi Berkurang');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));
        
        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Status');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row+1));
        
        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $abjad = 'A';
        
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . ($row + 1))->applyFromArray($style_judul_kolom);
        $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        // dd($res);
        foreach ($res as $value) {
            // dd($value->toArray());
            $no++;
            $col = 1;
            $row++;
            $value = $value[0];
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row+3));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + 3))->applyFromArray($style_kolom);

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama); //nama gudang
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + 3));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + 3))->applyFromArray($style_kolom);

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama); //nama pallet
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + 3));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + 3))->applyFromArray($style_kolom);
            
            $kondisi = [
                'Terpakai',
                'Tidak Terpakai',
                'Rusak',
                'Dasaran',
            ];

            $col++;
            $abjad++;
            for ($i=0; $i<count($kondisi); $i++) {
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $kondisi[$i]); //kondisi pallet
                $row++;
            }

            $col++;
            //stok awal
            $row = $row-count($kondisi);
            $abjad++;
            for ($i = 0; $i < count($kondisi); $i++) {
                $materialTrans = MaterialTrans::where('id_material', $value->id_material)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->where('created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                if (!empty($materialTrans)) {
                    $masuk      = $materialTrans->where('tipe', 2)->sum('jumlah');
                    $keluar     = $materialTrans->where('tipe', 1)->sum('jumlah');
                    $saldoAwal  = $masuk - $keluar;
                    
                    $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwal); //jumlah stok pallet per kondisi
                    $stokAwal[$i] = $saldoAwal;
                    $stokAkhir[$kondisi[$i]] = $saldoAwal;
                } else {
                    $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 0); //jumlah stok pallet per kondisi
                    $stokAwal[$i] = 0;
                    $stokAkhir[$kondisi[$i]] = 0;
                }
                $row++;
            }
            
            $col++;
            $stokAkhir[0] = 0;
            $stokAkhir[1] = 0;
            $stokAkhir[2] = 0;
            $stokAkhir[3] = 0;

            $tempPenambahan[0] = 0;
            $tempPenambahan[1] = 0;
            $tempPenambahan[2] = 0;
            $tempPenambahan[3] = 0;
            foreach ($gudang as $item) {
                $row = $row - count($kondisi);
                $abjad++;
                for ($i = 0; $i < count($kondisi); $i++) {
                    $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item) {
                        $query->where('id_gudang', $item->id);
                    })
                    ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
                    ->where('tipe', 2)
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                    $stokAkhir[$i] += $materialTrans;
                    $tempPenambahan[$i] = $tempPenambahan[$i]+$materialTrans;
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah pallet bertambah per gudang per kondisi
                    $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                    $row++;
                }
                $col++;
            }
            $abjadPemasukan++;
            $row = $row - count($kondisi);
            $abjad++;
            for ($i = 0; $i < count($kondisi); $i++) {
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPenambahan[$i]); //total pallet bertambah per kondisi
                $row++;
            }
            
            $col++;
            $tempPengeluaran[0] = 0;
            $tempPengeluaran[1] = 0;
            $tempPengeluaran[2] = 0;
            $tempPengeluaran[3] = 0;
            foreach ($gudang as $item) {
                $row = $row - count($kondisi);
                $abjad++;
                for ($i = 0; $i < count($kondisi); $i++) {
                    $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item) {
                        $query->where('id_gudang', $item->id);
                    })
                        ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
                        ->where('tipe', 1)
                        ->where('created_at', '>=', date('Y-m-d', strtotime($tgl_awal)))
                        ->where('created_at', '<=', date('Y-m-d', strtotime($tgl_akhir)))
                        ->where('id_material', $value->id_material)
                        ->sum('jumlah');
                    $stokAkhir[$i] -= $materialTrans;
                    $tempPengeluaran[$i] += $materialTrans;
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah pallet berkurang per gudang per kondisi
                    $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                    $row++;
                }
                $col++;
            }
            $row = $row - count($kondisi);
            $abjadPemasukan++;
            $abjad++;
            for ($i = 0; $i < count($kondisi); $i++) {
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPengeluaran[$i]); //total pengeluaran per kondisi
                $row++;
            }

            $col++;
            $tempPenyusutan[0] = 0;
            $tempPenyusutan[1] = 0;
            $tempPenyusutan[2] = 0;
            $tempPenyusutan[3] = 0;
            foreach ($yayasan as $item) {
                $row = $row - count($kondisi);
                $abjad++;
                for ($i = 0; $i < count($kondisi); $i++) {
                    $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
                        $query->where('id_gudang', $value->gudang->id);
                        $query->where('id_yayasan', $item->id);
                    })
                        ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
                        ->where('tipe', 1)
                        ->where('created_at', '>=', date('Y-m-d', strtotime($tgl_awal)))
                        ->where('created_at', '<=', date('Y-m-d', strtotime($tgl_akhir)))
                        ->where('id_material', $value->id_material)
                        ->sum('jumlah');
                    $stokAkhir[$i] -= $materialTrans;
                    $tempPenyusutan[$i] += $materialTrans;
                    $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah penyusutan per gudang per kondisi
                    $row++;
                }
                $col++;
            }
            $row = $row - count($kondisi);
            $abjadPemasukan++;
            $abjad++;
            for ($i = 0; $i < count($kondisi); $i++) {
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPenyusutan[$i]); //total yayasan
                $row++;
            }
            // // $col++;
            $rusak = 0;
            $materialTrans = MaterialTrans::where('tipe', 1)
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->sum('jumlah');

            if ($materialTrans) {
                $rusak += $materialTrans;
            }

            $row = $row - count($kondisi);
            
            for ($i = 0; $i < count($kondisi); $i++) {
                // dd($col);
                // $dipinjam = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
                //     $query->whereNotNull('peminjaman');
                // })
                //     ->where('tipe', 1)
                //     ->where('status_produk', ($i + 2))
                //     ->where('id_material', $value->id_material)
                //     ->sum('jumlah');
                // $col++;
                // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dipinjam);
                // $stokAkhir[$i] -= $dipinjam;
                
                $dipinjam = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
                    $query->whereNotNull('peminjaman');
                })
                    ->where('tipe', 1)
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] += $dipinjam;
                $col++;

                // $abjad++;
                // $abjadDipinjam = $abjad;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dipinjam);

               
                $dikembalikan = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
                    $query->whereNotNull('peminjaman');
                })
                    ->where('tipe', 2)
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] += $dikembalikan;
                $col++;
                // $abjad++;
                // $abjadDikembalikan = $abjad;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dikembalikan);

                $peralihanTambah = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) {
                    $query->whereNotNull('penyusutan');
                })
                    ->where('tipe', 2)
                    ->whereHas('aktivitasHarian', function($query){
                        $query->whereNotNull('id_yayasan');
                    })
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] += $peralihanTambah;
                $col++;
                // $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanTambah);

                $peralihanKurang = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) {
                    $query->whereNotNull('penyusutan');
                })
                    ->where('tipe', 1)
                    ->whereHas('aktivitasHarian', function($query){
                        $query->whereNotNull('id_yayasan');
                    })
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] += $peralihanKurang;
                $col++;
                // $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanKurang);

                
                if ($peralihanTambah == $peralihanKurang) {
                    $status = 'BALANCE';
                } else {
                    $status = 'CEKLAGI';
                }
                $col++;
                // $abjad++;
                // dd($abjad . $row);
                // $objSpreadsheet->getActiveSheet()->setCellValue($abjad.$row, '=IF('.$abjadDipinjam.$row.'='. $abjadDikembalikan.$row. ',"BALANCE","CEKLAGI")');
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $status);

                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir[$i]);
                $row++;
                $col -= 6;
                // print_r($col);
            }
            $row--;
            $abjad = 'A';
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Mutasi Pallet');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save('php://output');
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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanKeluhanGp()
    {
        $data['title']      = 'Laporan Keluhan GP';
        $data['gudang']     = Gudang::gp()->get();
        $data['keluhan']    = Keluhan::all();
        $data['aktivitas']  = Aktivitas::whereNotNull('pengiriman')->get();
        $data['produk']     = Material::produk()->get();
        return view('report.keluhan-gp.grid', $data);
    }

    public function keluhanGp()
    {
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $keluhan            = request()->input('keluhan'); //multi
        $kegiatan           = request()->input('kegiatan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir')));
        $res = AktivitasKeluhanGp::select(
            'aktivitas_keluhan_gp.*',
            'g.nama as nama_gudang',
            'm.nama as nama_material',
            'ah.created_at as tanggal'
            )
            ->leftJoin('aktivitas_harian as ah', 'aktivitas_keluhan_gp.id_aktivitas_harian', '=', 'ah.id')
            ->leftJoin('material as m', 'm.id', '=', 'aktivitas_keluhan_gp.id_material')
            ->leftJoin('gudang as g', 'g.id', '=', 'ah.id_gudang')
            ->whereBetween('ah.created_at', [date('Y-m-d', strtotime($tgl_awal)), date('Y-m-d', strtotime($tgl_akhir))])
            ->where(function ($query) use ($pilih_produk, $produk) {
                if ($produk == 2) {
                    foreach ($pilih_produk as $key => $value) {
                        $query->orWhere('m.id', $value);
                    }
                }
            })
            ;

        if ($keluhan) {
            $res = $res->whereHas('aktivitasHarian', function ($query) use ($keluhan) {
                $query->where('id', $keluhan[0]);
                foreach ($keluhan as $key => $value) {
                    $query->orWhere('id', $value);
                }
            });
        }

        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
        }
        
        if ($kegiatan) {
            $res = $res->where('aktivitasHarian.aktivitas',function ($query) use ($kegiatan) {
                $query->where('id_aktivitas', $kegiatan[0]);
                foreach ($kegiatan as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
        }

        $res = $res->orderBy('ah.created_at')->get();
        $nama_file = date("YmdHis") . '_keluhan_gp.xlsx';
        $this->generateExcelKeluhanGp($res, $nama_file, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelKeluhanGp($res, $nama_file, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: styles
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
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
        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
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
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        $style_isi_kolom = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            )
        );
        //end: styles

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Keluhan GP');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Aktivitas '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir)));
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang Penyangga');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Keluhan');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Pupuk');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kuantum');

        $abjad = 'A';
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ":" . 'F' . $row)->applyFromArray($style_judul_kolom);
        // $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':' . $abjad . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d/m/Y', strtotime($value->tanggal)));

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keluhan);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_material);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);

            $col++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Keluhan GP");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanMaterial()
    {
        $data['title'] = 'Laporan Material';
        $data['gudang'] = Gudang::internal()->get();
        $data['produk'] = Material::produk()->get();
        return view('report.material.grid', $data);
    }

    public function material(Request $request)
    {
        // $validator = Validator::make(
        //     $request->all(),[
        //     'gudang' => 'required',
        // ],[
        //     'required' => ':attribute wajib diisi!',
        // ],[
        //     'gudang' => 'Gudang'
        // ]);

        // if ($validator->fails()) {
        //     return redirect('report/laporan-material')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        $gudang             = request()->input('gudang'); //multi
        $material             = request()->input('material');
        $pilih_material       = request()->input('pilih_material'); //multi
        $tgl_awal           = request()->input('tgl_awal') == null? '' : date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = request()->input('tgl_awal') == null ? '' : date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        // $res = GudangStok::distinct()->select(
        //     'id_material',
        //     'id_gudang'
        // )
        // ->with('material')
        // ->with('gudang')
        // ->where(function ($query) use ($gudang) {
        //     foreach ($gudang as $key => $value) {
        //         $query = $query->orWhere('id_gudang', $value);
        //     }
        // })
        // ;

        // if ($pallet == 2) {
        //     $res = $res->where(function ($query) use ($pilih_pallet) {
        //         foreach ($pilih_pallet as $key => $value) {
        //             $query = $query->orWhere('id_material', $value);
        //         }
        //     });
        // } else {
        //     $res = $res->whereHas('material', function ($query) {
        //         $query = $query->where('kategori', 2);
        //     });
        // }

        // $res = $res->orderBy('id_gudang')->get();

        $res = MaterialTrans::with('aktivitasHarian', 'aktivitasHarian.gudang', 'aktivitasHarian.gudangTujuan')
        ->with('material')
        ->whereNotNull('id_aktivitas_harian')
        ->whereHas('material', function($query) {
            $query->where('kategori', 1);
        })
        
        ->whereBetween('tanggal', [date('Y-m-d', strtotime($tgl_awal)), date('Y-m-d', strtotime($tgl_akhir))])
        ->orderBy('id', 'asc')
        ;

        if ($gudang != null) {
            $res = $res->whereHas('aktivitasHarian', function ($query) use ($gudang) {
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id_gudang', $value);
                }
            });
        }

        if ($material == 2) {
            $res = $res->where(function ($query) use ($pilih_material) {
                foreach ($pilih_material as $key => $value) {
                    $query = $query->orWhere('id_material', $value);
                }
            });
        } else {
            $res = $res->whereHas('material', function ($query) {
                $query = $query->where('kategori', 1);
            });
        }

        $res = $res->get();
        // dd($res->toArray());
        

        $nama_file = date("YmdHis") . '_material.xlsx';
        $this->generateExcelMaterial($res, $nama_file, $tgl_awal, $tgl_akhir);

        // dd($res->toArray());
    }

    public function generateExcelMaterial($res, $nama_file, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: styles
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
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );

        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

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
        //end: styles

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Material');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peridode: '.date('d/m/Y', strtotime($tgl_awal)).' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Nama Material');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Status');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jumlah');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Asal');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tujuan');
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        // $abjadOri++;
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Asal');

        // // $gudang = Gudang::all();
        // $abjadPemasukan = $abjadOri;
        // $i = 0;
        // $row = 6;
        // // foreach ($gudang as $key) {
        // //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
        // //     $i++;
        // //     $col++;
        // //     $abjadPemasukan++;
        // // }
        // $row = 5;
        
        // $abjadPemasukan = chr(ord($abjadPemasukan) - 1);
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadPemasukan . $row);
        
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tujuan');
        // $i = 0;
        // $row = 6;
        // $abjadPengeluaran = $abjadPemasukan;
        // foreach ($gudang as $key) {
        //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
        //     $i++;
        //     $col++;
        //     $abjadPengeluaran++;
        // }
        // $row = 5;
        // $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . $row . ':' . $abjadPengeluaran . $row);
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        // $abjadPengeluaran++;
        // $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row+1));
        $abjad = 'A';
        
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadOri . $row)->applyFromArray($style_judul_kolom);
        // $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        $totalStok = 0;
        $totalRusak = 0;
        $totalNormal = 0;
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjad . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->tipe == 1?'Mengurangi':'Menambah');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y', strtotime($value->tanggal)));

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudang))?$value->aktivitasHarian->gudang->nama:'');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudangTujuan))?$value->aktivitasHarian->gudangTujuan->nama:'');

            if ($value->tipe == 1) {
                $totalStok -= $value->jumlah;
            } else {
                $totalStok += $value->jumlah;
            }

            if ($value->status_produk == 2) {
                if ($value->tipe == 1) {
                    $totalRusak -= $value->jumlah;
                } else {
                    $totalRusak += $value->jumlah;
                }
            }

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $style_isi_kolom = array(

                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
        }
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . 5 . ":" . $abjadOri . $row)->applyFromArray($style_kolom);

        $totalNormal = $totalStok-$totalRusak;
        
        $row++;
        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Totak Stok');    
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalStok);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Totak Rusak');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalRusak);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Totak Normal');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalNormal);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        // $abjad++;
        $abjad2 = chr(ord($abjad) + 1);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . ($row - 2) . ":" . $abjad2 . $row)->applyFromArray($style_kolom);
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Material");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    public function laporanStok()
    {
        $data['title'] = 'Laporan Stok';
        $data['gudang'] = Gudang::all();
        $data['produk'] = Material::produk()->get();
        return view('report.stok.grid', $data);
    }

    public function stok()
    {
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $tgl_awal   = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        // $res = Area::whereBetween('created_at', [$tgl_awal, $tgl_akhir]);

        // if ($gudang) {
        //     $res = $res->where(function ($query) use ($gudang) {
        //         $query->where('id_gudang', $gudang[0]);
        //         foreach ($gudang as $key => $value) {
        //             $query->orWhere('id_gudang', $value);
        //         }
        //     });
        // } else {
        //     $res = $res->whereHas('gudang', function($query) {
        //         $query->where('tipe_gudang', 1);
        //     });
        // }

        $res = MaterialTrans::whereBetween('created_at', [date('Y-m-d', strtotime($tgl_awal)), date('Y-m-d', strtotime($tgl_akhir))])->with('areaStok', 'areaStok.area');

        $resProduk = new Material;
        if ($produk == 2) {
            $res = $res->whereHas('areaStok', function ($query) use ($pilih_produk) {
                foreach ($pilih_produk as $key => $value) {
                    $query = $query->orWhere('id_material', $value);
                }
            });
            $resProduk = $resProduk->where(function($query) use ($pilih_produk) {
                foreach ($pilih_produk as $key => $value) {
                    $query->orWhere('id', $value);
                }
            });
        } else {
            $res = $res->whereHas('areaStok.material', function ($query) use($resProduk){
                $query = $query->where('kategori', 1);
                $resProduk = $resProduk->where('kategori', 1);
            });
        }

        $resArea = Area::where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            })->get();

        $res = $res->get()->groupBy('id_material');

        $resProduk = $resProduk->get();
        $nama_file = date("YmdHis") . '_stok.xlsx';
        $this->generateExcelStok($res, $nama_file, $resProduk, $resArea, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelStok($res, $nama_file, $produk, $area, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
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
        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_isi_kolom = array(

            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            )
        );
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
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        //end: style

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Stok (Bulan)');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Tanggal '.date('d/m/Y', strtotime($tgl_awal)). ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Area');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kapasitas');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Produk');

        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($produk as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
        }

        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . ($row - 1) . ':' . $abjadPemasukan . ($row - 1));       

        $abjad = 'A';

        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPemasukan . ($row + 1))->applyFromArray($style_judul_kolom);
        $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        $total = [];
        $total_kapasitas = 0;
        $total_kesamping = 0;
        $j=0;
        foreach ($area as $value) {
            $no++;
            $col = 1;
            $row++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPemasukan . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':' . $abjadPemasukan . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama); //nama area

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->kapasitas); //kapasitas
            $total_kapasitas += $value->kapasitas;
            $i = 0;
            $total_kesamping = 0;
            // dd($produk);
            foreach ($produk as $key) {
                // dd($key->id);
                $materialTrans = MaterialTrans::whereBetween('created_at', [date('Y-m-d', strtotime($tgl_awal)), date('Y-m-d', strtotime($tgl_akhir))])
                ->where('id_material', $key->id)
                ->whereHas('areaStok.area', function($query) use($value){
                    $query->where('id_area', $value->id);
                })
                ->get();
                $jumlah = 0;
                foreach ($materialTrans as $key2) {
                    if ($key2->tipe == 1) {
                        $jumlah = $jumlah - $key2->jumlah;
                    } else {
                        $jumlah = $jumlah + $key2->jumlah;
                    }
                }

                // $areaStok = AreaStok::where('id_area', $value->id)->where('id_material', $key->id)->sum('jumlah');
                $col++;
                // dd($jumlah);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $jumlah); //jumlah
                $total_kesamping += $jumlah;

                if (array_key_exists($i, $total)) {
                    $total[$i] = $total[$i] + $jumlah; 
                } else {
                    $total[$i] = $jumlah; 
                }
                $i++;
            }
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total_kesamping); //jumlah disamping

            $abjad = 'A';
            $j++;
        }

        $col=2;
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jumlah'); //jumlah

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total_kapasitas); //jumlah
        $total_semua = 0;
        $abjadKedua = 'C';
        for ($i=0; $i < count($total); $i++) {
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total[$i]); //jumlah
            $total_semua += $total[$i];
            $abjadKedua++;
        }
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total_semua); //jumlah
        // dd($abjadKedua);
        $abjadKedua++;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadKedua . $row)->applyFromArray($style_judul_kolom);
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Stok');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save('php://output');
    }
    
    public function laporanAbsenKaryawan()
    {
        $data['title'] = 'Laporan Absen Karyawan';
        $data['produk'] = Material::produk()->get();
        return view('report.karyawan.grid', $data);
    }

    public function laporanMutasiStok()
    {
        $data['title'] = 'Laporan Mutasi Stok';
        $data['produk'] = Material::produk()->get();
        return view('report.mutasi-stok.grid', $data);
    }

    public function mutasiStok()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'produk'    => 'required',
                'tgl_awal'  => 'required',
                'tgl_akhir' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'produk'    => 'Produk',
                'tgl_awal'  => 'Tanggal Awal',
                'tgl_akhir' => 'Tanggal Akhir',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-mutasi-stok')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $tgl_awal   = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir')));

        $res = Material::produk()
        ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
        ->where(function ($query) use ($pilih_produk, $produk) {
            if ($produk == 2) {
                foreach ($pilih_produk as $key => $value) {
                    $query->orWhere('id', $value);
                }
            }
        })
        ->get();

        $nama_file = date("YmdHis") . '_mutasi_stok.xlsx';
        $this->generateExcelMutasiStok($res, $nama_file, $tgl_awal, $tgl_akhir);
        // dd($res);
    }

    public function generateExcelMutasiStok($res, $nama_file, $tgl_awal, $tgl_akhir)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
                // 'size' => 18,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
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
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );

        $style_isi_kolom = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            )
        );
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
        //end: style

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Harian Mutasi Stock Gudang Gresik I & II');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Departemen Distribusi Wilayah I');
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':E' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)));
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('No')+7);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Produk');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('Produk')+10);
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('Stok Awal'));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

        $gudang = Gudang::all();
        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen($key->nama));
            $i++;
            $col++;
            $abjadPemasukan++;
        }
        $row = 5;
        
        $abjadPemasukan = chr(ord($abjadPemasukan) - 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadPemasukan . $row);
        
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pengeluaran');
        $i = 0;
        $row = 6;
        $abjadPengeluaran = $abjadPemasukan;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen($key->nama));
            $i++;
            $col++;
            $abjadPengeluaran++;
        }

        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . $row . ':' . $abjadPengeluaran . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row+1));
        $abjad = 'A';
        
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadPengeluaran . ($row+1))->applyFromArray($style_judul_kolom);
        $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadPengeluaran . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama);

            $material_trans = MaterialTrans::where('id_material', $value->id)
            ->orderBy('id', 'asc')
            ->first();

            $col++;
            if (!empty($material_trans)) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $material_trans->jumlah);
            }
            
            $materialTrans1 = MaterialTrans::where('id_material', $value->id)->get();
            // dd($materialTrans1->toArray());
            $stokAkhir = $material_trans['jumlah'];
            // if ($materialTrans1) {
            //     foreach ($materialTrans1 as $key) {
                    foreach ($gudang as $item) {
                        $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function($query) use ($value, $item){
                            $query->where('id_gudang', $item->id);
                            // $query->where('id_gudang_tujuan', $value->id_gudang);
                        })
                        ->where('tipe', 2)
                        ->whereNotNull('id_aktivitas_harian')
                        ->where('id_material', $value->id)
                        ->sum('jumlah');
                        $stokAkhir += $materialTrans;
                        $col++;
                        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
                    }
            //     }
            // }

            // if ($materialTrans1) {
            //     foreach ($materialTrans1 as $key) {
                    foreach ($gudang as $item) {
                        $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use($item, $value) {
                            $query->where('id_gudang_tujuan', $item->id);
                            // $query->whereNotNull('id_gudang_tujuan');
                        })
                        ->where('tipe', 1)
                        ->whereNotNull('id_aktivitas_harian')
                        ->where('id_material', $value->id)
                        ->sum('jumlah');
                        $stokAkhir -= $materialTrans;
                        $col++;
                        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
                    }
            //     }
            // }

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Mutasi Stok");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        $writer = new Xlsx($objSpreadsheet);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }
}
