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
use App\Jobs\GenerateExcel;
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
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir').'+1 day'));

        $res = AktivitasHarian::with('aktivitas')
        ->with('gudang')
        ->with('materialTrans.material')
        ->where('created_at', '>=', $tgl_awal)
        ->where('created_at', '<=', $tgl_akhir)
        ->where('draft', 0)
        ->whereHas('aktivitas', function($query) {
            $query->whereNull('peminjaman');
            $query->whereNull('penerimaan_gi');
        })
        ->whereHas('materialTrans.material', function($query) {
            $query->where('kategori', 1);
        })
        ->orderBy('created_at', 'asc')
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
            if (is_array($jenis_alat_berat)) {
                $query->where('id_kategori', $jenis_alat_berat[0]);
                foreach ($jenis_alat_berat as $key => $value) {
                    $query->orWhere('id_kategori', $value);
                }
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL '.date('d/m/Y', strtotime($tgl_awal)).' - '.date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'SHIFT');
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

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":G" . $row)->applyFromArray($style_judul_kolom);
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
            
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":G" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));

            $col++;
            $shiftKerja = ShiftKerja::withoutGlobalScopes()->find($value->id_shift); 
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $shiftKerja->nama);
            
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->aktivitas->kode_aktivitas.' - '.$value->aktivitas->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama);
            $col++;

            $users = Users::withoutGlobalScopes()->find($value->created_by);
            $tkbm = TenagaKerjaNonOrganik::withoutGlobalScopes()->find($users->id_tkbm);
            if ($tkbm) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tkbm->nama);
            } else {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            }

            $col++;
            $temp = '';
            // dd($value->materialTrans);
            foreach ($value->materialTrans as $key) {
                if ($key->material->kategori == 1){
                    if ($temp == '') {
                        $temp = $key->material->nama;
                    } else {
                        $temp = $temp.', '. $key->material->nama;
                    }
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
        $data['title'] = 'Laporan Material';
        $data['gudang'] = Gudang::internal()->get();
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
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir').'+1 day'));

        $res = AreaStok::distinct()->select(
            'id_material',
            'id_area'
        )
        ->with('material')
        ->with('area', 'area.gudang')
        ->where('status', 1);

        $resGudang = Gudang::internal()->get();
        if ($gudang) {
            $resGudang = Gudang::where(function($query) use ($gudang){
                $query = $query->where('id', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id', $value);
                }
            })
            ->get();
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

        $res = $res->orderBy('id_material')->get()->groupBy('id_material');
        // dd($res->groupBy('id_material')->toArray());

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_material.xlsx';
        // GenerateExcel::dispatch('material', ['res' => $res, 'nama_file' => $nama_file, 'resGudang' => $resGudang, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
        $this->generateExcelProduk($res, $nama_file, $resGudang, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelProduk($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir)
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
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . strtoupper(helpDate($tgl_awal, 'li')) . ' - ' . strtoupper(helpDate(date('Y-m-d', strtotime($tgl_akhir.'-1 day')), 'li')));
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

        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($gudang as $key) {
            // dd($key);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
        }
        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) - 1);
        // dd($abjadPemasukan);
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
        // dd($abjadPengeluaran);
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
                'color' => array('rgb' => '8FAADC')
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
            $value = $value[0];
            // dd($value);
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

            //stok awal
            $materialTransMengurang = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where(function ($query) use ($value) {
                    $query->where('aktivitas_harian.id_gudang', $value->area->id_gudang);
                    $query->orWhere('material_adjustment.id_gudang', $value->area->id_gudang);
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.created_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.created_at', '<', $tgl_awal);
                })
                ->where('status_produk', 1)
                ->where('tipe', 1)
                ->sum('jumlah')
                ;
            $materialTransMenambah = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where(function ($query) use ($value) {
                    $query->where('aktivitas_harian.id_gudang', $value->area->id_gudang);
                    $query->orWhere('material_adjustment.id_gudang', $value->area->id_gudang);
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.created_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.created_at', '<', $tgl_awal);
                })
                ->where('status_produk', 1)
                ->where('tipe', 2)
                ->sum('jumlah');
            $stokAwal = $materialTransMenambah - $materialTransMengurang;

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAwal);

            $stokAkhir = $stokAwal;
            //pemasukan
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                ->where('tipe', 2)
                ->where('id_material', $value->id_material)
                ->where(function($query) use($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->where('status_produk', 1)
                ->sum('jumlah');

                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }

            //pengeluaran
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                ->where('tipe', 1)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->where('status_produk', 1)
                ->sum('jumlah');

                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir);

            $rusak = 0;

            //jumlah rusak
            $rusakTambah = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('tipe', 2)
                ->sum('jumlah');
            $rusakKurang = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('tipe', 1)
                ->sum('jumlah');

            $rusak = $rusakTambah - $rusakKurang;
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

            // $listOfGudang
            // for ($i='E'; $i<count($listOfGudang); $i++) {
            //     if (array_key_exists($i, $listOfGudang)) {
            //         $objSpreadsheet->getActiveSheet()->getColumnDimension($listOfGudang[$i])->setVisible(false);
            //     }
            // }
        }

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
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir').'+1 day'));

        $res = GudangStok::distinct()->select('id_gudang', 'id_material');
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
        ->orderBy('id_gudang', 'asc')->get();
        // dd($res);
        $nama_file = date("YmdHis") . '_mutasi_pallet.xlsx';
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Mutasi Pallet');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $kondisi = [
                'Terpakai',
                'Tidak Terpakai',
                'Rusak',
            ];

            $jumlahMerge = count($kondisi)-1;

            // $value = $value[0];
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama); //nama gudang
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);

            $col++;
            $abjad = chr(ord($abjad) + 1);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama); //nama pallet
            $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);
            
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
            $stokAkhir[0] = 0;
            $stokAkhir[1] = 0;
            $stokAkhir[2] = 0;
            $stokAkhir[3] = 0;
            $abjad++;
            for ($i = 0; $i < count($kondisi); $i++) {
                $masuk      = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use ($tgl_awal, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0)
                    ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                    ->where('aktivitas_harian.created_at', '<', date('Y-m-d', strtotime($tgl_awal)))
                    ;
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('gudang_stok', function ($join) {
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where('material_trans.id_material', $value->id_material)
                ->where('tipe', 2)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $keluar     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_awal, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where('aktivitas_harian.created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where('material_trans.id_material', $value->id_material)
                ->where('tipe', 1)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');
                // dd(($i+2));
                $saldoAwal  = $masuk - $keluar;
                
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwal); //jumlah stok pallet per kondisi
                $stokAwal[$i] = $saldoAwal;
                $stokAkhir[$i] = $saldoAwal;
                $row++;
            }
            
            $col++;

            $tempPenambahan[0] = 0;
            $tempPenambahan[1] = 0;
            $tempPenambahan[2] = 0;
            $tempPenambahan[3] = 0;
            foreach ($gudang as $item) {
                $row = $row - count($kondisi);
                $abjad++;
                for ($i = 0; $i < count($kondisi); $i++) {
                    $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
                        $query->where(function($query) use($item, $value) {
                            $query->where('id_gudang', $item->id);
                            $query->where('id_gudang_tujuan', $value->id_gudang);
                        });
                        $query->where('draft', 0);
                    })
                    ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
                    ->where('tipe', 1)
                    ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
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
                    $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
                        $query->where(function ($query) use ($item, $value) {
                            $query->where('id_gudang', $item->id);
                            $query->where('id_gudang_tujuan', $value->id_gudang);
                        });
                        $query->where('draft', 0);
                    })
                        ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
                        ->where('tipe', 2)
                        ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
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
                $abjadDalam = $abjad;
                $dipinjam = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
                    $query->whereNotNull('peminjaman');
                })
                    ->where('tipe', 1)
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] -= $dipinjam;
                $col++;

                $abjadDalam++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dipinjam);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
               
                $dikembalikan = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
                    $query->whereNotNull('peminjaman');
                })
                    ->where('tipe', 2)
                    ->where('status_produk', ($i + 2))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $stokAkhir[$i] += $dikembalikan;
                $col++;
                $abjadDalam++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dikembalikan);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);

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
                $abjadDalam++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanTambah);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);

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
                $stokAkhir[$i] -= $peralihanKurang;
                $col++;
                $abjadDalam++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanKurang);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);

                
                if ($peralihanTambah == $peralihanKurang) {
                    $status = 'BALANCE';
                } else {
                    $status = 'CEKLAGI';
                }
                $col++;
                $abjadDalam++;
                // $objSpreadsheet->getActiveSheet()->setCellValue($abjad.$row, '=IF('.$abjadDipinjam.$row.'='. $abjadDikembalikan.$row. ',"BALANCE","CEKLAGI")');
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $status);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);

                $col++;
                $abjadDalam++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir[$i]);
                $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
                $row++;
                $col -= 6;
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
        $data['gudang']     = Gudang::internal()->get();
        $data['produk']     = Material::produk()->get();
        $data['shift']      = ShiftKerja::orderBy('nama', 'asc')->get();
        $data['aktivitas']  = Aktivitas::nonPenerimaanGi()->get();
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
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = DB::table('aktivitas_harian')->select(
            'aktivitas.nama',
            'aktivitas_harian.created_at as tanggal',
            'g.nama as nama_gudang', 
            'id_shift',
            'm.nama as nama_material',
            'mt.jumlah'
            )
            ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->leftJoin('gudang as g', 'g.id', '=', 'aktivitas_harian.id_gudang')
            ->leftJoin('material_trans as mt', 'mt.id_aktivitas_harian', '=', 'aktivitas_harian.id')
            ->leftJoin('material as m', 'm.id', '=', 'id_material')
            ->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir])
            ->where('draft', 0)
            ->whereNull('peminjaman')
            ->whereNull('penerimaan_gi')
            ->where('kategori', 1)
            ->orderBy('aktivitas_harian.created_at', 'asc')
            ;
        
        // dd($res->get()->toArray());

        // $res = RealisasiMaterial::select(
        //     'realisasi_material.id',
        //     'tipe',
        //     'm.nama',
        //     'mt.jumlah',
        //     'realisasi_material.tanggal',
        //     'gs.id_gudang',
        //     'realisasi_material.id_shift',
        //     'g.nama as nama_gudang'
        // )
        // ->leftJoin('material_trans as mt', 'realisasi_material.id', '=', 'id_realisasi_material')
        // ->leftJoin('material as m', 'm.id', '=', 'id_material')
        // ->leftJoin('gudang_stok as gs', 'gs.id_material', '=', 'mt.id_material')
        // ->leftJoin('gudang as g', 'g.id', '=', 'gs.id_gudang')
        // ->whereBetween('realisasi_material.tanggal', [$tgl_awal, $tgl_akhir]);

        if ($shift) {
            $res = $res->where(function ($query) use ($shift) {
                foreach ($shift as $key => $value) {
                    $query->orWhere('id_shift', $value);
                }
            });
        }
        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
        }

        if ($kegiatan) {
            // dd($kegiatan);
            $res = $res->where(function ($query) use ($kegiatan) {
                foreach ($kegiatan as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
        }

        if ($produk == 2) {
            $res = $res->where(function ($query) use ($pilih_produk,$produk) {
                if ($produk == 2) {
                    foreach ($pilih_produk as $key => $value) {
                        $query->orWhere('m.id', $value);
                    }
                }
            });
        } else {

        }

        $res = $res->get();

        $nama_file = date("YmdHis") . '_realisasi.xlsx';
        $this->generateExcelRealisasi($res, $nama_file, $kegiatan, $shift, $tgl_awal, $tgl_akhir);
    }

    public function generateExcelRealisasi($res, $nama_file, $kegiatan, $shift, $tgl_awal, $tgl_akhir)
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
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'LAPORAN REALISASI');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $textKegiatan = 'SEMUA KEGIATAN';
        if ($kegiatan) {
            $aktivitas = Aktivitas::find($kegiatan[0]);
            $textKegiatan = 'KEGIATAN '.strtoupper($aktivitas->nama);
            for ($i=1; $i<count($kegiatan); $i++) {
                $aktivitas = Aktivitas::find($kegiatan[$i]);
                $textKegiatan .= ', '. strtoupper($aktivitas->nama);
            }
        }
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $textKegiatan);
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $textPeriode = 'SEMUA SHIFT';
        if ($shift) {
            $textPeriode = 'SHIFT ' . $shift[0];
            for ($i=1; $i<count($shift);$i++) {
                $textPeriode .= 'SHIFT '.$shift[$i];
            }
        }

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PERIODE '.$textPeriode.' TANGGAL ' . strtoupper(helpDate($tgl_awal, 'li')) . ' - ' . strtoupper(helpDate($tgl_akhir, 'li')));
        // $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

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
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kegiatan');
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

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":G" . $row)->applyFromArray($style_judul_kolom);
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

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":G" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_material);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d/m/Y', strtotime($value->tanggal)));
            
            $col++;
            $shiftKerja = ShiftKerja::find($value->id_shift);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $shiftKerja->nama);
            
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama);

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
        // $keluhan            = request()->input('keluhan'); //multi
        $kegiatan           = request()->input('kegiatan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));
        $res = AktivitasKeluhanGp::select(
            'aktivitas_keluhan_gp.*',
            'g.nama as nama_gudang',
            'm.nama as nama_material',
            'ah.created_at as tanggal'
            )
            ->leftJoin('aktivitas_harian as ah', 'aktivitas_keluhan_gp.id_aktivitas_harian', '=', 'ah.id')
            ->leftJoin('material as m', 'm.id', '=', 'aktivitas_keluhan_gp.id_material')
            ->leftJoin('gudang as g', 'g.id', '=', 'ah.id_gudang')
            ->whereBetween('ah.created_at', [$tgl_awal, $tgl_akhir])
            ->where(function ($query) use ($pilih_produk, $produk) {
                if ($produk == 2) {
                    foreach ($pilih_produk as $key => $value) {
                        $query->orWhere('m.id', $value);
                    }
                }
            })
            ;

        // if ($keluhan) {
        //     // $res = $res->whereHas('keluhan', function ($query) use ($keluhan) {
        //         $res = $res->where('id_keluhan', $keluhan[0]);
        //         foreach ($keluhan as $key => $value) {
        //             $res = $res->orWhere('id_keluhan', $value);
        //         }
        //     // });
        // }

        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
        }
        
        if ($kegiatan) {
            // $res = $res->where('aktivitasHarian.aktivitas',function ($query) use ($kegiatan) {
                $res = $res->where('ah.id_aktivitas', $kegiatan[0]);
                foreach ($kegiatan as $key => $value) {
                    $res = $res->orWhere('ah.id_aktivitas', $value);
                }
            // });
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Aktivitas '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
            $abjad = 'A';
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':' . $abjad . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d/m/Y', strtotime($value->tanggal)));
            $abjad++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $abjad++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keluhan);
            $abjad++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_material);
            $abjad++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);
            $abjad++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

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
        $data['title'] = 'Laporan Transaksi Material';
        $data['gudang'] = Gudang::internal()->get();
        $data['produk'] = Material::produk()->get();
        return view('report.material.grid', $data);
    }

    public function material()
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
        $tgl_akhir          = request()->input('tgl_akhir') == null ? '' : date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

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
        ->leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
        ->whereHas('material', function($query) {
            $query->where('kategori', 1);
        })
        ->whereHas('aktivitasHarian', function($query) {
            $query->where('draft', 0);
        })
        ->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir])
        ->orderBy('material_trans.id', 'asc')
        ;

        if ($gudang != null) {
            $res = $res->whereHas('aktivitasHarian', function ($query) use ($gudang) {
                $query = $query->where('id_gudang', $gudang[0]);
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

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_transaksi_material.xlsx';
        // $genToExcel = (new GenerateExcel('transaksi_material', ['res' => $res, 'nama_file' => $nama_file, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]))->onQueue('material');
        // $this->dispatch($genToExcel);
        $this->generateExcelMaterial($res, $nama_file, $tgl_awal, $tgl_akhir);
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

        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        //end: styles

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Transaksi Material');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peridode: '.date('d/m/Y', strtotime($tgl_awal)).' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        $jumlahStok = 0;
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
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y', strtotime($value->created_at)));

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudang))?$value->aktivitasHarian->gudang->nama:'');

            $col++;
            if ($value->aktivitasHarian->so == null) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudangTujuan)) ? $value->aktivitasHarian->gudangTujuan->nama:'');
            } else {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->so)) ? $value->aktivitasHarian->so : '');
            }
            
            $tempRes =  DB::table('material_trans')
            ->leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
            ->where('id_material', $value->material->id)
            ->where('draft', 0)
            ->where('aktivitas_harian.created_at', '<', $tgl_awal);

            $penambahan = $tempRes->where('tipe', 2)->sum('jumlah');
            $pengurangan = $tempRes->where('tipe', 1)->sum('jumlah');

            $jumlahStok = $penambahan+$pengurangan;

            if ($value->tipe == 1) {
                $totalStok -= $value->jumlah;
            } else {
                $totalStok += $value->jumlah;
            }

            $totalStok += $jumlahStok;

            if ($value->status_produk == 2) {
                if ($value->tipe == 1) {
                    $totalRusak -= $value->jumlah;
                } else {
                    $totalRusak += $value->jumlah;
                }
            }

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
        }
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . 5 . ":" . $abjadOri . $row)->applyFromArray($style_kolom);

        $totalNormal = $totalStok-$totalRusak;
        
        $row++;
        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Stok');    
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalStok);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Rusak');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalRusak);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Normal');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalNormal);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);

        // $abjad++;
        $abjad2 = chr(ord($abjad) + 1);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . ($row - 2) . ":" . $abjad2 . $row)->applyFromArray($style_kolom);
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Transaksi Material");
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
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

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

        // $res = MaterialTrans::whereBetween('created_at', [$tgl_awal, $tgl_akhir])
        // ->with('areaStok', 'areaStok.area')
        // ->whereHas('areaStok', function ($query){
        //     $query->where('status', 1);
        // })
        // ;
        $res = DB::table('area')->distinct()->select('area.id', 'area.nama', 'kapasitas')
        ->join('area_stok', 'area_stok.id_area', '=', 'area.id')
        ->join('material_trans', 'material_trans.id_area_stok', '=', 'area_stok.id')->where('area_stok.jumlah', '>', 0)
        ->where('area_stok.status', 1)
        ;

        $resProduk = Material::produk();
        if ($produk == 2) {
            // $res = $res->whereHas('areaStok', function ($query) use ($pilih_produk) {
            //     foreach ($pilih_produk as $key => $value) {
            //         $query = $query->orWhere('id_material', $value);
            //     }
            // });
            $resProduk = $resProduk->where(function($query) use ($pilih_produk) {
                foreach ($pilih_produk as $key => $value) {
                    $query->orWhere('id', $value);
                }
            });
        } else {
            // $res = $res->whereHas('areaStok.material', function ($query) use($resProduk){
            //     $query = $query->where('kategori', 1);
            //     $resProduk = $resProduk->where('kategori', 1);
            // });
        }
        $resProduk = $resProduk->get();
        if (is_array($gudang)) {
            $resArea = DB::table('area')->distinct()->select('area.*')
            // ->leftJoin('area_stok', 'area_stok.id_area', '=', 'area.id')
            // ->leftJoin('material_trans', 'material_trans.id_area', '=', 'area.id')
            // ->leftJoin('aktivitas_harian', 'material_trans.id_aktivitas_harian', '=', 'aktivitas_harian.id')
            // ->leftJoin('material', 'area_stok.id_material', '=', 'material.id')
            ->where(function ($query) use ($gudang) {
                $query->where('area.id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('area.id_gudang', $value);
                }
            })     
            // ->whereBetween('material_trans.created_at', [$tgl_awal, $tgl_akhir])       
            // ->where('area_stok.jumlah', '>', 0)
            // ->where('kategori', 1)
            // ->where('draft', 0)
            ->get();

            $res = $res->where(function ($query) use ($gudang) {
                $query->where('area.id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('area.id_gudang', $value);
                }
            });

        } else {
            $resArea = DB::table('area')
            // ->join('area_stok', 'area_stok.id_area', '=', 'area.id')
            // ->where('jumlah', '>', 0)
            ->get();
        }

        $res = $res->get();

        $nama_file = date("YmdHis") . '_stok.xlsx';
        // dd($res->toArray());
        // dispatch(new GenerateExcel('stok', ['res' => $res, 'nama_file' => $nama_file, 'resProduk' => $resProduk, 'resArea' => $resArea, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]));
        // GenerateExcel::dispatch('stok', ['res' => $res, 'nama_file' => $nama_file, 'resProduk' => $resProduk, 'resArea' => $resArea, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Tanggal '.date('d/m/Y', strtotime($tgl_awal)). ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
            // dd($value);
            foreach ($produk as $key) {
                $jumlah =0;
                $singleton = DB::table('material_trans')->where('id_material', $key->id)
                    ->where('status_produk', 1) //harus + 2 step agar cocok dengan status pada databse
                    ->where('material_trans.id_area', $value->id)
                    ->leftJoin('aktivitas_harian', function ($join) use ($value) {
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')->where('draft', 0);
                    });

                $stokTanggalIni = $singleton->whereBetween('material_trans.created_at', [$tgl_awal, $tgl_akhir])->get();
                
                $stokTanggalSebelum = $singleton->where('material_trans.created_at', '<', $tgl_awal)->get();
                
                $masuk = 0;
                $keluar = 0;
                foreach ($stokTanggalIni as $singletonKey) {
                    if ($singletonKey->tipe == 2) {
                        $masuk = (float)$masuk + (float)$singletonKey->jumlah;
                    } else if ($singletonKey->tipe == 1) {
                        $keluar = (float)$keluar + (float)$singletonKey->jumlah;
                    }
                }

                $pre_masuk = 0;
                $pre_keluar = 0;
                foreach ($stokTanggalSebelum as $preKey) {
                    if ($preKey->tipe == 2) {
                        $pre_masuk = (float)$pre_masuk + (float)$preKey->jumlah;
                    } else if ($preKey->tipe == 1) {
                        $pre_keluar = (float)$pre_keluar + (float)$preKey->jumlah;
                    }
                }

                // $masuk      = $singleton
                //     ->where('material_trans.tipe', 2)
                //     ->sum('jumlah');

                // $keluar     = $singleton
                //     ->where('material_trans.tipe', 1)
                //     ->sum('jumlah');

                // dd($masuk);

                $jumlah  = ((float)$pre_masuk - (float)$pre_keluar)+((float)$masuk - (float)$keluar);
                // $materialTrans = DB::table('material_trans')->whereBetween('created_at', [$tgl_awal,$tgl_akhir])
                // ->where('id_material', $key->id)
                // ->where('status_produk', 1)
                // ->where('id_area', $value->id)
                // ->get();
                // foreach ($materialTrans as $key2) {
                //     if ($key2->tipe == 1) {
                //         $jumlah = $jumlah - $key2->jumlah;
                //     } else {
                //         $jumlah = $jumlah + $key2->jumlah;
                //     }
                // }

                $col++;
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
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = MaterialTrans::distinct()->select(
            'id_material',
            'm.nama'
        )
        ->leftJoin('material as m', 'm.id', '=', 'material_trans.id_material')
        ->leftJoin('aktivitas_harian as ah', 'ah.id', '=', 'material_trans.id_aktivitas_harian')
        ->leftJoin('material_adjustment as ma', 'ma.id', '=', 'material_trans.id_adjustment')
        ->where('draft', 0)
        ->where(function($query) use($tgl_awal, $tgl_akhir) {
            $query->whereBetween('ah.created_at', [$tgl_awal, $tgl_akhir]);
            $query->orWhereBetween('ma.created_at', [$tgl_awal, $tgl_akhir]);
        })
        ;

        if ($produk == 2) {
            $res = $res->where(function ($query) use ($pilih_produk) {
                $query = $query->where('material_trans.id_material', $pilih_produk[0]);
                foreach ($pilih_produk as $key => $value) {
                    $query = $query->orWhere('material_trans.id_material', $value);
                }
            });
        } else {
            $res = $res->where('kategori', 1);
        }
        
        // $res = Material::produk()
        // ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
        // ->where(function ($query) use ($pilih_produk, $produk) {
        //     if ($produk == 2) {
        //         foreach ($pilih_produk as $key => $value) {
        //             $query->orWhere('id', $value);
        //         }
        //     }
        // })
        // ->get();

        // dd($res->get());

        $nama_file = date("YmdHis") . '_mutasi_stok.xlsx';
        $this->generateExcelMutasiStok($res->get(), $nama_file, $tgl_awal, $tgl_akhir);
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
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('No')+7);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Produk');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('Produk')+10);
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen('Stok Awal'));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

        $gudang = Gudang::internal()->get();
        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen($key->nama));
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
            // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setWidth(strlen($key->nama));
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
        // dd($res);
        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadPengeluaran . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama);

            $materialTransMengurang = MaterialTrans::
                // leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                // ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                leftJoin('aktivitas_harian', function ($join) use ($tgl_awal) {
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal) {
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                // ->where(function ($query) use ($value) {
                //     $query->where('aktivitas_harian.id_gudang', $value->id_gudang);
                //     $query->orWhere('material_adjustment.id_gudang', $value->id_gudang);
                // })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.created_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.created_at', '<', $tgl_awal);
                })
                ->where('status_produk', 1)
                ->where('tipe', 1)
                ->sum('jumlah');

            $materialTransMenambah = MaterialTrans::
                // leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                // ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                // ->where(function ($query) use ($value) {
                //     $query->where('aktivitas_harian.id_gudang', $value->id_gudang);
                //     $query->orWhere('material_adjustment.id_gudang', $value->id_gudang);
                // })
                leftJoin('aktivitas_harian', function ($join) use ($tgl_awal) {
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal) {
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.created_at', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.created_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.created_at', '<', $tgl_awal);
                })
                ->where('status_produk', 1)
                ->where('tipe', 2)
                ->sum('jumlah');

            $stokAwal = $materialTransMenambah - $materialTransMengurang;
            // dd($stokAwal);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAwal);
            $stokAkhir = $stokAwal;
            // $materialTrans1 = MaterialTrans::where('id_material', $value->id)->get();
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian as ah', 'ah.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment as ma', 'ma.id', '=', 'material_trans.id_adjustment')
                ->where('ah.id_gudang', $item->id)
                ->where('tipe', 2)
                ->where(function($query) use($tgl_awal, $tgl_akhir){
                    $query->whereBetween('ah.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('ma.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('id_material', $value->id_material)
                ->where('draft', 0)
                ->where('status_produk', 1)
                ->sum('jumlah');
                
                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian as ah', 'ah.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment as ma', 'ma.id', '=', 'material_trans.id_adjustment')
                ->where('ah.id_gudang', $item->id)
                ->where('tipe', 1)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('ah.created_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('ma.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('id_material', $value->id_material)
                ->where('draft', 0)
                ->where('status_produk', 1)
                ->sum('jumlah');

                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }

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
