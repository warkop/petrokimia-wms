<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\Keluhan;
use App\Http\Models\KeluhanOperator;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use App\Http\Models\Yayasan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\IOFactory;

class ReportController extends Controller
{
    private $AKTIVITAS_UPDATED_AT_FULLDATE = "TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')";
    private $FORMAT_FULLDATE = 'Y-m-d H:i:s';
    private $FORMAT_DATE = 'Y-m-d';
    private $START_SHIFT3 = ' 23:00:00 -1 day';
    private $START_SHIFT1 = ' 07:00:00';
    private $START_SHIFT2 = ' 15:00:00';
    private $INCREMENT_DAY = "+1 day";
    private $DECREMENT_DAY = "-1 day";
    private $style_note;
    private $style_judul_kolom;
    private $style_acara;
    private $style_title;
    private $style_center;
    private $style_kolom;
    private $style_no;
    private $style_vertical_center;

    public function __construct()
    {
        //start: style
        $this->style_note = array(
            'font' => array(
                'bold' => true
            )
        );
        $this->style_judul_kolom = array(
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
        $this->style_acara = array(
            'font' => array(
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $this->style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $this->style_center = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $this->style_kolom = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
        );
        $this->style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        );
        $this->style_vertical_center['alignment'] = array(
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        );
        //end: style   
    }

    //untuk memperoleh informasi checker ini sekarang berada di gudang mana
    private function getCheckerGudang($id_role)
    { 
        if ($id_role == 3) {
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm', auth()->user()->id_tkbm)
                ->orderBy('rencana_harian.id', 'desc')
                ->take(1)->first();

            if (empty($rencana_tkbm)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Checker tidak terdaftar pada rencana harian apapun!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            $rencana_harian = RencanaHarian::findOrFail($rencana_tkbm->id_rencana);
            $gudang = Gudang::findOrFail($rencana_harian->id_gudang);
        } else if ($id_role == 5) {
            $karu   = Karu::find(auth()->user()->id_karu);
            $gudang = Gudang::find($karu->id_gudang);
        } else {
            return false;
        }

        return $gudang;
    }

    public function laporanAktivitas()
    {
        $data['title'] = 'Laporan Aktivitas';
        $data['aktivitas'] = Aktivitas::whereNull('penerimaan_gi')->get();
        $data['shift'] = ShiftKerja::get();
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        return view('report.aktivitas.grid', $data);
    }

    public function aktivitasHarian()
    {
        $validator = Validator::make(
            request()->all(),[
            'tgl_awal' => 'required|before_or_equal:tgl_akhir',
            'tgl_akhir' => 'required|after_or_equal:tgl_awal',
        ],[
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute harus lebih dari atau sama dengan :date!',
            'before_or_equal' => ':attribute harus kurang dari atau sama dengan :date!',
        ],[
            'tgl_awal' => 'Tanggal Awal',
            'tgl_akhir' => 'Tanggal Akhir',
        ]);

        if ($validator->fails()) {
            return redirect('report/laporan-aktivitas')
                ->withErrors($validator)
                ->withInput();
        }

        $aktivitas  = request()->aktivitas;
        $shift      = request()->shift;
        $gudang     = request()->gudang;
        $tgl_awal   = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir  = date('Y-m-d', strtotime(request()->input('tgl_akhir').'+1 day'));

        $res = AktivitasHarian::with('aktivitas')
        ->with('gudang')
        ->with('materialTrans.material')
        ->where('updated_at', '>=', $tgl_awal)
        ->where('updated_at', '<=', $tgl_akhir)
        ->where('draft', 0)
        ->whereHas('aktivitas', function($query) {
            $query->whereNull('penerimaan_gi');
        })
        ->whereHas('materialTrans.material', function($query) {
            $query->where('kategori', 1);
        })
        ->orderBy('updated_at', 'asc')
        ;

        if (!empty($aktivitas)) {
            $res = $res->where(function ($query) use($aktivitas){
                $query->where('id_aktivitas', $aktivitas[0]);
                foreach ($aktivitas as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
        }

        if (!empty($gudang)) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
        }

        if (!empty($shift)) {
            $res = $res->where(function ($query) use ($shift) {
                $query->where('id_shift', $shift[0]);
                foreach ($shift as $key => $value) {
                    $query->orWhere('id_shift', $value);
                }
            });
        }

        $res = $res->orderBy('aktivitas_harian.updated_at', 'asc')->get();
        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_aktivitas_harian.xlsx';
        $this->generateExcelAktivitas($res, $nama_file, $tgl_awal, $tgl_akhir, $preview);
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
        ->with('gudang')
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
        ->where('jenis', '2');

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $res = $res->where('id_gudang', $localGudang->id);
        }

        $res = $res->get();

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_kerusakan_alat_berat.xlsx';
        $this->generateExcelKeluhanAlatBerat($res, $nama_file, $preview);
    }

    public function generateExcelAktivitas($res, $nama_file, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Aktivitas Harian');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL '.date('d/m/Y', strtotime($tgl_awal)).' - '.date('d/m/Y', strtotime($tgl_akhir . '-1 day')));

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


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KUANTUM');


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
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":H" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
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
            
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":H" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray($style_no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->updated_at)));

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
            $kuantum = '';

            foreach ($value->materialTrans as $key) {
                if ($key->material->kategori == 1){
                    if ($temp == '') {
                        $temp = $key->material->nama;
                    } else {
                        $temp = $temp.', '. $key->material->nama;
                    }

                    if ($kuantum == '') {
                        if ($key->tipe == 1) {
                            $kuantum = '-' . round($key->jumlah, 3);
                        } else {
                            $kuantum = round($key->jumlah, 3);
                        }
                    } else {
                        if ($key->tipe == 1) {
                            $kuantum = $kuantum . ', ' . '-' . round($key->jumlah, 3);
                        } else {
                            $kuantum = $kuantum . ', ' . round($key->jumlah, 3);
                        }
                    }
                }
            }
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $temp);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $kuantum);
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle("H" . $row)->applyFromArray($style_no);

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
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function generateExcelKeluhanAlatBerat($res, $nama_file, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kerusakan Alat Berat');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

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
        $style_no = array(
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
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        // $objSpreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

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
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Nama Gudang');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Keluhan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No. Lambung');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Keterangan');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dokumentasi');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tindak Lanjut Rekanan');
        // $col++;
        // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal Tindak Lanjut');


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
            )
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":H" . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;

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

            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row . ":H" . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->kategori->nama);
            $objSpreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray($style_no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->gudang))?$value->gudang->nama:'-');
            $objSpreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray($style_no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->kerusakan->nama);
            $objSpreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray($style_no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->alatBerat->nomor_lambung);
            $objSpreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray($style_no);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keterangan);
            $col++;

            $temp = '';
            $x = 5;
            $y = 5;
            foreach ($value->foto as $row2) {
                $temp .= $row2->file_enc;
                
                if (!empty($value->id) && file_exists(storage_path("/app/public/history/" . $value->id . "/" . $row2->file_enc))) {
                    $image_url = base_url() . "application/storage/app/public/history/" . $value->id . "/" . $row2->file_enc;
                    if (isset($image_url) && !empty($image_url)) {
                        if (strpos($image_url, ".png") === false) {
                            $image_resource = imagecreatefromjpeg($image_url);
                        } else {
                            $image_resource = imagecreatefrompng($image_url);
                        }
                        $objDrawing = new MemoryDrawing;
                        $objDrawing->setName($row2->file_ori);
                        $objDrawing->setDescription('gambar ' . $row2->file_ori);
                        $objDrawing->setImageResource($image_resource);
                        $objDrawing->setCoordinates(strtoupper(toAlpha($col - 1)) . $row);
                        //setOffsetX works properly
                        $objDrawing->setOffsetX($x);
                        $objDrawing->setOffsetY($y);
                        //set width, height
                        $objDrawing->setWidth(120);
                        $objDrawing->setWorksheet($objSpreadsheet->getActiveSheet());
                        // $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(110);
                        
                        $y += $objDrawing->getHeight();
                        $objSpreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($y);
                    }
                } else {
                    $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "File tidak ada di server ");
                }
            }
            // $col++;
            // $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->status==0?"Belum":"Sudah");

            // $lap = LaporanKerusakan::where('induk', $value->id)->where('status', 1)->orderBy('id', 'desc')->first();
            
            // if (!empty($lap)) {
            //     $col++;
            //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, helpDate($lap->created_at, 'si'));
            // }

            $style_no['alignment'] = array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            );
            $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Keluhan Alat Berat");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanProduk()
    {
        $data['title'] = 'Laporan Produk';

        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }

        $data['gudang'] = $gudang->get();
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
        ->join('area', 'area.id', '=', 'area_stok.id_area')
        ->join('gudang', 'gudang.id', '=', 'area.id_gudang')
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

        $res = $res
        // ->orderBy('gudang.nama', 'asc')
        ->orderBy('id_material', 'asc')
        ->get()
        ->groupBy('id_material');

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_produk.xlsx';
        $this->generateExcelProduk($res, $nama_file, $resGudang, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelProduk($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        // start : title
        $col = 1;
        $row = 1;

        $abjadTitle = 'D';
        foreach ($gudang as $key) {
            $abjadTitle++;
        }

        foreach ($gudang as $key) {
            $abjadTitle++;
        }

        $abjadTitle++;
        $abjadTitle++;
        $abjadTitle++;

        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Produk');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . strtoupper(helpDate($tgl_awal, 'li')) . ' - ' . strtoupper(helpDate(date('Y-m-d', strtotime($tgl_akhir.'-1 day')), 'li')));
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


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Produk');
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
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
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
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
        }
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row-1) . ':' . $abjadPengeluaran . ($row-1));
        
        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);

        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Rusak');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);

        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $col++;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Siap Jual');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
        
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $abjad = 'A';
        $style_judul_kolom = array(
            'fill' => array(
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

        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;
            $value = $value[0];

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
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->area->gudang->nama);

            //stok awal
            $materialTransMengurang = MaterialTrans::
            leftJoin('aktivitas_harian', function ($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
            })
            ->leftJoin('aktivitas', function ($join){
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                // ->whereNotNull('status_aktivitas')
                ;
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
            ->where(function ($query) use ($value) {
                $query->where('aktivitas_harian.id_gudang', $value->area->id_gudang);
                $query->orWhere('material_adjustment.id_gudang', $value->area->id_gudang);
            })
            ->where('id_material', $value->id_material)
            ->where(function ($query) use ($tgl_awal) {
                $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
            })
            ->where('tipe', 1)
            ->whereNotNull('status_aktivitas')
            ->sum('jumlah')
            ;
            $materialTransMenambah = MaterialTrans::
            leftJoin('aktivitas_harian', function ($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
            })
            ->leftJoin('aktivitas', function ($join){
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                // ->whereNotNull('status_aktivitas')
                ;
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
            ->where(function ($query) use ($value) {
                $query->where('aktivitas_harian.id_gudang', $value->area->id_gudang);
                $query->orWhere('material_adjustment.id_gudang', $value->area->id_gudang);
            })
            ->where('id_material', $value->id_material)
            ->where(function ($query) use ($tgl_awal) {
                $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
            })
            ->where('tipe', 2)
            ->whereNotNull('status_aktivitas')
            ->sum('jumlah')
            ;
            $stokAwal = $materialTransMenambah - $materialTransMengurang;

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAwal, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $stokAkhir = $stokAwal;

            $abjadPemasukan = 'E';

            //pemasukan
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join){
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    
                    ;
                })
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                ->where('tipe', 2)
                ->where('id_material', $value->id_material)
                ->where(function($query) use($tgl_awal, $tgl_akhir) {
                    // $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                    $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');

                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadPemasukan . $row)->applyFromArray($style_no);
                $abjadPemasukan++;
            }

            //pengeluaran
            $abjadPengeluaran = $abjadPemasukan;
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join){
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    // ->whereNotNull('status_aktivitas')
                    ;
                })
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($item) {
                    $query->where('id_gudang', $item->id);
                })
                ->where('tipe', 1)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    // $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                    $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');

                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadPengeluaran. $row)->applyFromArray($style_no);
                $abjadPengeluaran++;
            }

            $abjadNormal = $abjadPengeluaran;
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAkhir, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadNormal. $row)->applyFromArray($style_no);

            $rusak = 0;
            $rusakSaldoAwal = 0;
            
            //stok awal produk rusak
            $transRusakMenambah = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
            ->leftJoin('aktivitas', function ($join){
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ->whereNotNull('status_aktivitas')
                ;
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
            ->whereHas('areaStok.area', function ($query) use ($value) {
                $query->where('id_gudang', $value->area->id_gudang);
            })
            ->where('status_produk', 2)
            ->where('id_material', $value->id_material)
            ->where(function ($query) use ($tgl_awal) {
                $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
            })
            ->where('tipe', 2)
            ->sum('jumlah');

            $transRusakMengurang = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
            ->leftJoin('aktivitas', function ($join){
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ->whereNotNull('status_aktivitas')
                ;
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
            ->whereHas('areaStok.area', function ($query) use ($value) {
                $query->where('id_gudang', $value->area->id_gudang);
            })
            ->where('status_produk', 2)
            ->where('id_material', $value->id_material)
            ->where(function ($query) use ($tgl_awal) {
                $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
            })
            ->where('tipe', 1)
            ->sum('jumlah');

            $rusakSaldoAwal += $transRusakMenambah - $transRusakMengurang;

            //jumlah rusak
            $rusakTambah = 0;
            $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join){
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    ->whereNotNull('status_aktivitas')
                    ;
                })
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($value) {
                    $query->where('id_gudang', $value->area->id_gudang);
                })
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->where('tipe', 2)
                ->sum('jumlah');

            $rusakTambah += $materialTrans;

            $rusakKurang = 0;
            $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join){
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    ->whereNotNull('status_aktivitas')
                    ;
                })
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->whereHas('areaStok.area', function ($query) use ($value) {
                    $query->where('id_gudang', $value->area->id_gudang);
                })
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                    $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                })
                ->where('tipe', 1)
                ->sum('jumlah');
            
            $rusakKurang += $materialTrans;

            //total produk rusak
            $rusak = $rusakSaldoAwal + $rusakTambah - $rusakKurang;
            $col++;
            $abjadNormal++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($rusak, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadNormal. $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadNormal. $row)->applyFromArray($style_no);

            $siapJual = $stokAkhir-$rusak;
            
            $col++;
            $abjadNormal++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($siapJual, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadNormal . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadNormal . $row)->applyFromArray($style_no);

        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Produk");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanMaterial()
    {
        $data['title'] = 'Laporan Material';
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        $data['material'] = Material::where('kategori', 3)->orWhere('kategori', 2)->orderBy('kategori', 'asc')->get();
        return view('report.material.grid', $data);
    }

    public function material()
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
            return redirect('report/laporan-material')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $resPallet = GudangStok::select(
            'id_gudang',
            'id_material'
        )->distinct()->with('gudang');

        $resGudang = Gudang::internal()->get();
        
        if ($gudang) {
            $resGudang = Gudang::where(function ($query) use ($gudang) {
                $query = $query->where('id', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id', $value);
                }
            })
            ->get();

            $resPallet = $resPallet->where(function($query) use($gudang) {
                $query = $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id_gudang', $value);
                }
            });
        }

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $resPallet = $resPallet->get();

        $res = '';
        $nama_file = date("YmdHis") . '_material.xlsx';
        $this->generateExcelMaterial($res, $nama_file, $resGudang, $resPallet, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelMaterial($res, $nama_file, $gudang, $resPallet, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        // start : title
        $col = 3;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':F' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Material');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':F' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . strtoupper(helpDate($tgl_awal, 'li')) . ' - ' . strtoupper(helpDate(date('Y-m-d', strtotime($tgl_akhir . '-1 day')), 'li')));
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
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        );

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Material');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);

        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        // $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadOri)->setAutoSize(true);
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
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
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
        }
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row - 1) . ':' . $abjadPengeluaran . ($row - 1));

        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);

        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

        $abjad = 'A';
        $style_judul_kolom = array(
            'fill' => array(
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

        foreach ($resPallet as $value) {
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
            $objSpreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray($style_no);

            $col++;
            if (!empty($value->gudang)) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama);
            } else {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            }

            //stok awal
            $materialTransMengurang = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where(function ($query) use ($value) {
                    $query->where('aktivitas_harian.id_gudang', $value->id_gudang);
                    $query->orWhere('material_adjustment.id_gudang', $value->id_gudang);
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                })
                ->whereNull('status_produk')
                ->whereNotNull('status_pallet')
                ->where('tipe', 1)
                ->sum('jumlah');
            $materialTransMenambah = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                ->where(function ($query) use ($value) {
                    $query->where('aktivitas_harian.id_gudang', $value->id_gudang);
                    $query->orWhere('material_adjustment.id_gudang', $value->id_gudang);
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                })
                ->whereNull('status_produk')
                ->whereNotNull('status_pallet')
                ->where('tipe', 2)
                ->sum('jumlah');
            $stokAwal = $materialTransMenambah - $materialTransMengurang;

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAwal, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle("D" . $row)->applyFromArray($style_no);

            $stokAkhir = $stokAwal;
            $abjadPemasukan = 'E';
            //pemasukan
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                    ->whereHas('areaStok.area', function ($query) use ($item) {
                        $query->where('aktivitas_harian.id_gudang_tujuan', $item->id);
                    })
                    ->where(function($query) use($item){
                        $query->where('aktivitas_harian.id_gudang', $item->id);
                        $query->orWhere('material_adjustment.id_gudang', $item->id);
                    })
                    ->where('tipe', 1)
                    ->where('id_material', $value->id_material)
                    ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                        $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                    })
                    ->where('status_produk', 1)
                    ->sum('jumlah');

                $stokAkhir += $materialTrans;
                $col++;
                
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadPemasukan . $row)->applyFromArray($style_no);
                $abjadPemasukan++;
            }

            $abjadPengeluaran = $abjadPemasukan;
            // $abjadPengeluaran++;
            //pengeluaran
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                    ->whereHas('areaStok.area', function ($query) use ($item) {
                        $query->where('id_gudang', $item->id);
                    })
                    ->where(function ($query) use ($item) {
                        $query->where('aktivitas_harian.id_gudang_tujuan', $item->id);
                    })
                    ->where('tipe', 2)
                    ->where('id_material', $value->id_material)
                    ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                        $query->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                    })
                    ->where('status_produk', 1)
                    ->sum('jumlah');

                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadPengeluaran . $row)->applyFromArray($style_no);
                $abjadPengeluaran++;
            }
            $col++;
            // $abjadPengeluaran++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAkhir, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadPengeluaran . $row)->applyFromArray($style_no);
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Material");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanMutasiPallet()
    {
        $data['title'] = 'Laporan Pallet';
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }

        $data['gudang'] = $gudang->get();
        $data['pallet'] = Material::pallet()->get();
        return view('report.mutasi-pallet.grid', $data);
    }

    public function mutasiPallet()
    {
        $validator = Validator::make(
            request()->all(),[
            'tgl_awal' => 'required|before_or_equal:tgl_akhir',
            'tgl_akhir' => 'required|after_or_equal:tgl_awal',
        ],[
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute harus lebih dari atau sama dengan :date!',
            'before_or_equal' => ':attribute harus kurang dari atau sama dengan :date!',
        ],[
            'gudang' => 'Gudang',
            'tgl_awal' => 'Tanggal Awal',
            'tgl_akhir' => 'Tanggal Akhir',
        ]);

        if ($validator->fails()) {
            return redirect('report/laporan-mutasi-pallet')
                ->withErrors($validator)
                ->withInput();
        }

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

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_mutasi_pallet.xlsx';
        $this->generateExcelMutasiPallet($res, $nama_file, $resGudang, $tgl_awal, $tgl_akhir, $preview);
    }

    private function headerExcelMutasiPallet($objSpreadsheet, $tgl_awal, $tgl_akhir)
    {
        // start : title
        for ($i='A'; $i < 'X'; $i++) {
            $objSpreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
        }

        $abjadTitle = 'E';
        
        //start : incremental alphabet for adjustment horizontal center
        for ($i=0; $i < 25; $i++) {
            $abjadTitle++;
        }
        //end : incremental alphabet for adjustment horizontal center

        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Mutasi Pallet');
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($this->style_title);
        
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($this->style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($this->style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($this->style_note);
        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 2));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TGL');

        $abjadOri++; // B
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'SHIFT');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 2));

        // scope start "awal"
        $abjadOri++; // C
        $abjadAwal = $abjadOri;
        $abjadAwal++;
        $abjadAwal++;
        $abjadAwal++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AWAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadAwal . $row);

        $abjadBaik = $abjadOri;
        $abjadBaik++;
        $col = 3;
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BAIK');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadBaik . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');

        $abjadOri++; // D
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');

        $abjadOri++; //E
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');

        $abjadOri++; //F
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        // scope end "awal"
        
        // scope start "masuk dari"
        $abjadOri++; //G
        $abjadMasuk = $abjadOri;
        $abjadMasuk++;
        $abjadMasuk++;
        $abjadMasuk++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'MASUK DARI');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadMasuk . $row);

        $abjadOri++;
        $row++;
        $col = 7;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadMasuk . $row . ':' . $abjadMasuk . ($row+1));
        // scope end "masuk dari"

        // scope start "keluar ke"
        $abjadKeluar = $abjadOri; // J
        $abjadKeluar++;
        $abjadKeluar++;
        $abjadKeluar++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KELUAR KE');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadKeluar . $row);

        $abjadOri++;
        $row++;
        $col = 11;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'TOTAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadKeluar . $row . ':' . $abjadKeluar . ($row+1));
        // scope end "keluar ke"

        // scope start "susut"
        $abjadSusut = $abjadOri; // 
        $abjadSusut++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'SUSUT');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadSusut . $row);

        $abjadOri++;
        $row++;
        $col = 15;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'YPG');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'RUSAK');

        $abjadOri++; // Q
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'LAIN-LAIN');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'PAKAI');
        // scope end "susut"

        // scope start "Dipinjam"
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'DIPINJAM');

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));
        // scope end "Dipinjam"

        // scope start "Alih kondisi (+)"
        $abjadOri++; // R
        $abjadAlihPlus = $abjadOri; // R
        $abjadAlihPlus++;
        $abjadAlihPlus++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'ALIH KONDISI (+)');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadAlihPlus . $row);

        $row++;
        $col = 18;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));
        // scope end "Alih kondisi (+)"

        // scope start "Alih kondisi (-)"
        $abjadOri++;
        $abjadAlihMinus = $abjadOri; // J
        $abjadAlihMinus++;
        $abjadAlihMinus++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'ALIH KONDISI (-)');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadAlihMinus . $row);

        $row++;
        $col = 21;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));
        // scope end "Alih kondisi"

        // scope start "Alih kondisi"
        $abjadOri++; // T
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'ALIH KONDISI');

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BALANCE');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row+1));
        // scope end "Alih kondisi"

        // scope start "Akhir"
        $abjadOri++; // V
        $abjadAkhir = $abjadOri;
        $abjadAkhir++;
        $abjadAkhir++;
        $abjadAkhir++;
        $row = 5;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AKHIR');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadAkhir . $row);

        $abjadBaik = $abjadOri;
        $abjadBaik++;
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BAIK');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadBaik . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KOSONG');

        $abjadOri++; // W
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PAKAI');

        $abjadOri++; // X
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');

        $abjadOri++; // Y
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadAkhir . $row . ':' . $abjadAkhir . ($row + 1));
        // scope end "Akhir"

        // scope start "Total Pallet"
        $abjadOri++; // Z
        $abjadTotal = $abjadOri;
        $abjadTotal++;
        $col++;
        $row--;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL PALLET');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadTotal . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BAIK');

        $abjadOri++; // X
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'RUSAK');
        // scope end "Total Pallet"
        
        $row = 5;
        $abjad = 'A';
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadOri . ($row + 2))->applyFromArray($this->style_judul_kolom);
        $row = 7;
        // end : judul kolom
    }

    private function mutasiPalletGetFromShift1($res, $tgl_sekarang, $shift, $kondisi)
    {
        # code...
    }
    private function mutasiPalletGetFromShift2($res, $tgl_sekarang, $shift, $kondisi)
    {
        # code...
    }
    private function mutasiPalletGetFromShift3($res, $tgl_sekarang, $shift, $kondisi)
    {
        # code...
    }

    private function mutasiPalletGetStokAwal($res, $tgl_sekarang, $shift, $kondisi)
    {
        $saldoAwal = 0;
        // foreach ($res as $value) {
        //     $masuk      = MaterialTrans::
        //     leftJoin('aktivitas_harian', function($join) use ($tgl_sekarang, $value, $shift){
        //         $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
        //         ->where('draft', 0)
        //         ->where('aktivitas_harian.id_gudang', $value->id_gudang)
        //         ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00 -1 day')))
        //         ;
        //     })
        //     ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
        //         $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
        //             ->where('material_adjustment.id_gudang', $value->id_gudang)
        //             ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_sekarang)));
        //     })
        //     ->leftJoin('gudang_stok', function ($join) {
        //         $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
        //     })
        //     ->where('material_trans.id_material', $value->id_material)
        //     ->where('tipe', 2)
        //     ->where(function($query) use($shift) {
        //         $query->where('id_shift', $shift);
        //         $query->orWhere('shift_id', $shift);
        //         $query->orWhere('shift', $shift);
        //     })
        //     ->where('gudang_stok.id_gudang', $value->id_gudang)
        //     ->where('status_pallet', $kondisi)
        //     ->sum('material_trans.jumlah');

        //     $keluar     = MaterialTrans::
        //     leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
        //         $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
        //             ->where('draft', 0)
        //             ->where('aktivitas_harian.id_gudang', $value->id_gudang)
        //             ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00 -1 day')));
        //     })
        //     ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
        //         $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
        //             ->where('material_adjustment.id_gudang', $value->id_gudang)
        //             ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_sekarang)));
        //     })
        //     ->leftJoin('gudang_stok', function ($join){
        //         $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
        //     })
        //     ->where('material_trans.id_material', $value->id_material)
        //     ->where('tipe', 1)
        //     ->where(function($query) use($shift) {
        //         $query->where('id_shift', $shift);
        //         $query->orWhere('shift_id', $shift);
        //         $query->orWhere('shift', $shift);
        //     })
        //     ->where('gudang_stok.id_gudang', $value->id_gudang)
        //     ->where('status_pallet', $kondisi)
        //     ->sum('material_trans.jumlah');
        //     $saldoAwal  = $saldoAwal + $masuk - $keluar;
        // }

        foreach ($res as $value) {
            if ($shift == 2) {
                $pre_masuk     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                    $query->orWhere(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . ' 23:00:00')));
                        $query->where('id_shift', 1);
                    });
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_sekarang);
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                            $query->where('material_adjustment.tanggal', '=', $tgl_sekarang);
                            $query->where(function($query){
                                $query->where('material_trans.shift_id', 1);
                                $query->orWhere('material_trans.shift_id', 3);
                            });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 2)
                ->where('material_trans.id_material', $value->id_material)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $pre_keluar     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                    $query->orWhere(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . ' 23:00:00')));
                        $query->where('id_shift', 1);
                    });
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_sekarang);
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                            $query->where('material_adjustment.tanggal', '=', $tgl_sekarang);
                            $query->where(function($query){
                                $query->where('material_trans.shift_id', 1);
                                $query->orWhere('material_trans.shift_id', 3);
                            });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 1)
                ->where('material_trans.id_material', $value->id_material)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $saldoAwal = $saldoAwal + $pre_masuk - $pre_keluar;
            } else if ($shift == 1) {
                $pre_masuk     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT1)));
                        $query->orWhere(function($query) use($tgl_sekarang){
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT1)));
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                            $query->where('id_shift', 3);
                        });
                    });

                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_adjustment.tanggal', '<', $tgl_sekarang);
                        $query->orWhere(function($query) use ($tgl_sekarang){
                            $query->where('material_adjustment.tanggal', '=', $tgl_sekarang);
                            $query->where('material_adjustment.shift', '=', 3);
                        });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('tipe', 2)
                ->where('material_trans.id_material', $value->id_material)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $pre_keluar     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT1)));
                        $query->orWhere(function($query) use($tgl_sekarang){
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT1)));
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT2)));
                            $query->where('id_shift', 3);
                        });
                    });

                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_adjustment.tanggal', '<', $tgl_sekarang);
                        $query->orWhere(function($query) use ($tgl_sekarang){
                            $query->where('material_adjustment.tanggal', '=', $tgl_sekarang);
                            $query->where('material_adjustment.shift', '=', 3);
                        });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('tipe', 1)
                ->where('material_trans.id_material', $value->id_material)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $saldoAwal = $saldoAwal + $pre_masuk - $pre_keluar;
            } else if ($shift == 3) {
                $pre_masuk = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                    $query->orWhere(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . ' 00:30:00')));
                        $query->where('id_shift', 2);
                    });
                    $query->orWhere('material_adjustment.tanggal', '<=', date($this->FORMAT_DATE, strtotime($tgl_sekarang . $this->DECREMENT_DAY)));
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                            $query->where('material_adjustment.tanggal', '=', date($this->FORMAT_DATE, strtotime($tgl_sekarang . $this->DECREMENT_DAY)));
                            $query->where(function($query){
                                $query->where('material_trans.shift_id', 2);
                                $query->orWhere('material_trans.shift_id', 1);
                            });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 2)
                ->where('material_trans.id_material', $value->id_material)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $pre_keluar = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_sekarang, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_sekarang, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_sekarang)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                ->where(function ($query) use ($tgl_sekarang) {
                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                    $query->orWhere(function($query) use($tgl_sekarang){
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . $this->START_SHIFT3)));
                        $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_sekarang . ' 00:30:00')));
                        $query->where('id_shift', 2);
                    });
                    $query->orWhere('material_adjustment.tanggal', '<=', date($this->FORMAT_DATE, strtotime($tgl_sekarang . $this->DECREMENT_DAY)));
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                            $query->where('material_adjustment.tanggal', '=', date($this->FORMAT_DATE, strtotime($tgl_sekarang . $this->DECREMENT_DAY)));
                            $query->where(function($query){
                                $query->where('material_trans.shift_id', 2);
                                $query->orWhere('material_trans.shift_id', 1);
                            });
                    });
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', '<', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 1)
                ->where('material_trans.id_material', $value->id_material)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $saldoAwal = $saldoAwal + $pre_masuk - $pre_keluar;
            }
        }

        return $saldoAwal;
    }

    private function mutasiPalletGetPemasukan($res, $gudang, $tgl_sekarang, $shift, $kondisi)
    {
        $penambahan = 0;
        foreach ($res as $value) {
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value, $shift) {
                    $query->where(function($query) use($item, $value, $shift) {
                        $query->where('id_gudang', $item->id);
                        $query->where('id_gudang_tujuan', $value->id_gudang);
                    });
                    $query->where('id_shift', $shift);
                    $query->where('draft', 0);
                })
                ->where('status_pallet', $kondisi) //harus + 2 step agar cocok dengan status pada databse
                ->where('tipe', 1)
                ->where('created_at', $tgl_sekarang)
                ->where('id_material', $value->id_material)
                ->sum('jumlah');
                $penambahan = $penambahan+$materialTrans;
            }
        }

        return $penambahan;
    }

    private function mutasiPalletGetPengeluaran($res, $gudang, $tgl_sekarang, $shift, $kondisi)
    {
        $pengeluaran = 0;
        foreach ($res as $value) {
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value, $shift) {
                    $query->where(function ($query) use ($item, $value, $shift) {
                        $query->where('id_gudang', $item->id);
                        $query->where('id_gudang_tujuan', $value->id_gudang);
                        $query->where('id_shift', $shift);
                    });
                    $query->where('draft', 0);
                })
                    ->where('status_pallet', $kondisi)
                    ->where('tipe', 2)
                    ->where('created_at', $tgl_sekarang)
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $pengeluaran += $materialTrans;
            }
        }

        return $pengeluaran;
    }

    private function mutasiPalletPenyusutan($res, $yayasan, $tgl_sekarang, $shift, $kondisi)
    {
        $penyusutan = 0;
        foreach ($res as $value) {
            foreach ($yayasan as $item) {
                $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value, $shift) {
                    $query->where('id_gudang', $value->gudang->id);
                    $query->where('id_yayasan', $item->id);
                    $query->where('id_shift', $shift);
                })
                    ->where('status_pallet', $kondisi)
                    ->where('tipe', 1)
                    ->where('created_at', '>=', date('Y-m-d', strtotime($tgl_sekarang)))
                    ->where('created_at', '<=', date('Y-m-d', strtotime($tgl_sekarang)))
                    ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                $penyusutan += $materialTrans;
            }
        }
        return $penyusutan;
    }

    private function mutasiPalletPeminjaman($res, $gudang, $tgl_sekarang, $shift)
    {
        $peminjaman = 0;
        foreach ($res as $value) {
            $transaksi = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($shift) {
                $query->whereNotNull('peminjaman');
                $query->where('id_shift', $shift);
            })
            ->where('tipe', 1)
            ->where('id_material', $value->id_material)
            ->sum('jumlah');
            $peminjaman += $transaksi;
        }

        return $peminjaman;
    }

    private function mutasiPalletPeralihanBerkurang($res, $tgl_sekarang, $shift, $kondisi='')
    {
        $peralihanBerkurang = 0;
        foreach ($res as $value) {
            //start : transaksi keluar
            $transaksi = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0)
                    ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                    ;
                })
                ->leftJoin('material_adjustment', function($join) use($value) {
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                    ->where('material_adjustment.id_gudang', $value->id_gudang)
                    ;
                })
                ->leftJoin('gudang_stok', function($join) use($value) {
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ;
                })
                ->where(function($query) use($tgl_sekarang){
                    $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00'))]);
                    $query->orWhere('material_adjustment.created_at', $tgl_sekarang);
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 1)
                ->where(function($query) use($shift) {
                    $query->where('id_shift', $shift);
                    $query->orWhere('shift_id', $shift);
                    $query->orWhere('shift', $shift);
                })
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi)
                ->where('material_trans.id_material', $value->id_material)
                ->sum('material_trans.jumlah');
            
            $peralihanBerkurang += $transaksi;
            //end : transaksi keluar
        }

        return $peralihanBerkurang;
    }

    private function mutasiPalletPeralihanBertambah($res, $tgl_sekarang, $shift, $kondisi='')
    {
        $peralihanBertambah = 0;
        foreach ($res as $value) {
            //start : transaksi masuk
            $transaksi = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0)
                    ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                    ;
                })
                ->leftJoin('material_adjustment', function($join) use($value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                    ->where('material_adjustment.id_gudang', $value->id_gudang)
                    ;
                })
                ->leftJoin('gudang_stok', function($join) use($value) {
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ;
                })
                ->where(function($query) use($tgl_sekarang){
                    $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_sekarang . ' 23:00:00'))]);
                    $query->orWhere('material_adjustment.created_at', $tgl_sekarang);
                    $query->orWhere(function ($query) use ($tgl_sekarang) {
                        $query->where('material_trans.tanggal', $tgl_sekarang);
                        $query->whereNull('material_trans.id_aktivitas_harian');
                        $query->whereNull('material_trans.id_adjustment');
                    });
                })
                ->where('tipe', 2)
                ->where(function($query) use($shift) {
                    $query->where('id_shift', $shift);
                    $query->orWhere('shift_id', $shift);
                    $query->orWhere('shift', $shift);
                })
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', $kondisi)
                ->where('material_trans.id_material', $value->id_material)
                ->sum('material_trans.jumlah');
            
            $peralihanBertambah += $transaksi;
            //end : transaksi masuk
        }
        
        return $peralihanBertambah;
    }

    public function generateExcelMutasiPallet($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();
        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        // end : sheet
        
        // start : draw title
        $this->headerExcelMutasiPallet($objSpreadsheet, $tgl_awal, $tgl_akhir);
        // end : draw title

        // start : isi kolom
        $no = 0;

        $shifts = [3,1,2];

        $tgl_sekarang = $tgl_awal;
        $row = 8;
        do {
            $col=1;
            $abjadIncrement = 'A';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d', strtotime($tgl_sekarang)));
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
            $objSpreadsheet->getActiveSheet()->mergeCells($abjadIncrement . $row . ':' . $abjadIncrement . ($row + 3));
            $totalMasukKosong   = 0; 
            $totalMasukPakai    = 0; 
            $totalMasukRusak    = 0; 
            $totalMasuk         = 0;
            
            $totalKeluarKosong  = 0; 
            $totalKeluarPakai   = 0; 
            $totalKeluarRusak   = 0; 
            $totalKeluar        = 0;

            $totalSusutYpg = 0; 
            $totalSusutLainlain = 0; 

            $totalAlihKondisiPlusKosong = 0;
            $totalAlihKondisiPlusPakai = 0;
            $totalAlihKondisiPlusRusak = 0;

            $totalAlihKondisiMinusKosong = 0;
            $totalAlihKondisiMinusPakai = 0;
            $totalAlihKondisiMinusRusak = 0;
            
            $totalDipinjam = 0;
             
            foreach ($shifts as $shift) {
                $stokAkhirKosong = 0;
                $stokAkhirPakai = 0;
                $stokAkhirRusak = 0;

                $col=2;
                $abjadIncrement = 'B';
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, helpRoman($shift));
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                
                $saldoAwalKosong    = $this->mutasiPalletGetStokAwal($res, $tgl_sekarang, $shift, 3);
                $saldoAwalPakai     = $this->mutasiPalletGetStokAwal($res, $tgl_sekarang, $shift, 2);
                $saldoAwalRusak     = $this->mutasiPalletGetStokAwal($res, $tgl_sekarang, $shift, 4);
                
                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwalKosong); //jumlah stok pallet kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirKosong += $saldoAwalKosong;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwalPakai); //jumlah stok pallet terpakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirPakai += $saldoAwalPakai;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwalRusak); //jumlah stok pallet rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirRusak += $saldoAwalRusak;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwalKosong+$saldoAwalPakai+$saldoAwalRusak); //total stok awal
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $pemasukanKosong = $this->mutasiPalletGetPemasukan($res, $gudang, $tgl_sekarang, $shift, 3);
                $pemasukanPakai = $this->mutasiPalletGetPemasukan($res, $gudang, $tgl_sekarang, $shift, 2);
                $pemasukanRusak = $this->mutasiPalletGetPemasukan($res, $gudang, $tgl_sekarang, $shift, 4);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pemasukanKosong); //jumlah pemasukan pallet kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirKosong += $pemasukanKosong;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pemasukanPakai); //jumlah pemasukan pallet terpakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirPakai += $pemasukanPakai; 

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pemasukanRusak); //jumlah pemasukan pallet rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirRusak += $pemasukanRusak;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pemasukanKosong+$pemasukanPakai+$pemasukanRusak); //jumlah pemasukan pallet rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $pengeluaranKosong = $this->mutasiPalletGetPengeluaran($res, $gudang, $tgl_sekarang, $shift, 3);
                $pengeluaranPakai = $this->mutasiPalletGetPengeluaran($res, $gudang, $tgl_sekarang, $shift, 2);
                $pengeluaranRusak = $this->mutasiPalletGetPengeluaran($res, $gudang, $tgl_sekarang, $shift, 4);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pengeluaranKosong); //jumlah pengeluaran pallet kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirKosong -= $pengeluaranKosong;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pengeluaranPakai); //jumlah pengeluaran pallet terpakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirPakai -= $pengeluaranPakai;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pengeluaranRusak); //jumlah pengeluaran pallet rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirRusak -= $pengeluaranRusak;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $pengeluaranKosong+$pengeluaranPakai+$pengeluaranRusak); //jumlah pengeluaran pallet rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $penyusutanRusak = $this->mutasiPalletPenyusutan($res, $gudang, $tgl_sekarang, $shift, 4);
                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $penyusutanRusak); //jumlah penyusutan Rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirRusak -= $penyusutanRusak;

                $penyusutanPakai = $this->mutasiPalletPenyusutan($res, $gudang, $tgl_sekarang, $shift, 2);
                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $penyusutanPakai); //jumlah penyusutan Pakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirPakai -= $penyusutanPakai;

                $peminjaman = $this->mutasiPalletPeminjaman($res, $gudang, $tgl_sekarang, $shift);
                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peminjaman); //jumlah peminjaman
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $stokAkhirKosong -= $peminjaman;
                
                $peralihanBertambahKosong   = $this->mutasiPalletPeralihanBertambah($res, $tgl_sekarang, $shift, 3);
                $peralihanBertambahPakai    = $this->mutasiPalletPeralihanBertambah($res, $tgl_sekarang, $shift, 2);
                $peralihanBertambahRusak    = $this->mutasiPalletPeralihanBertambah($res, $tgl_sekarang, $shift, 4);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBertambahKosong); //jumlah alih kondisi (+) kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiPlusKosong += $peralihanBertambahKosong;
                $stokAkhirKosong += $peralihanBertambahKosong;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBertambahPakai); //jumlah alih kondisi (+) pakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiPlusPakai += $peralihanBertambahPakai;
                $stokAkhirPakai += $peralihanBertambahPakai;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBertambahRusak); //jumlah alih kondisi (+) rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiPlusRusak += $peralihanBertambahRusak;
                $stokAkhirRusak += $peralihanBertambahRusak;

                $peralihanBerkurangKosong   = $this->mutasiPalletPeralihanBerkurang($res, $tgl_sekarang, $shift, 3);
                $peralihanBerkurangPakai    = $this->mutasiPalletPeralihanBerkurang($res, $tgl_sekarang, $shift, 2);
                $peralihanBerkurangRusak    = $this->mutasiPalletPeralihanBerkurang($res, $tgl_sekarang, $shift, 4);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBerkurangKosong); //jumlah alih kondisi (-) kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiMinusKosong += $peralihanBerkurangKosong;
                $stokAkhirKosong -= $peralihanBerkurangKosong;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBerkurangPakai); //jumlah alih kondisi (-) pakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiMinusPakai += $peralihanBerkurangPakai;
                $stokAkhirPakai -= $peralihanBerkurangPakai;

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanBerkurangRusak); //jumlah alih kondisi (-) rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
                $totalAlihKondisiMinusRusak += $peralihanBerkurangRusak;
                $stokAkhirRusak -= $peralihanBerkurangRusak;

                $peralihanBertambah = $peralihanBertambahKosong+$peralihanBertambahPakai+$peralihanBertambahRusak;
                $peralihanBerkurang = $peralihanBerkurangKosong+$peralihanBerkurangPakai+$peralihanBerkurangRusak;

                $status = 'CEK LAGI';

                if ($peralihanBertambah == $peralihanBerkurang) {
                    $status = 'BALANCE';
                }

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $status); //kondisi
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirKosong); //stok akhir kosong
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirPakai); //stok akhir pakai
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirRusak); //stok akhir rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirKosong+$stokAkhirPakai+$stokAkhirRusak); //total stok akhir
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirKosong+$stokAkhirPakai); //total akhir baik
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $col++;
                $abjadIncrement++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhirRusak); //total akhir rusak
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
                $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

                $row++;
            }
            $col = 2;
            $abjadIncrement = 'B';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TOTAL');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
            
            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
            
            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
            
            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);
            
            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalMasukKosong);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalMasukPakai);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalMasukRusak);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalMasuk);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalKeluarKosong);$abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalKeluarPakai);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalKeluarRusak);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalKeluar);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalSusutYpg);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalSusutLainlain);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalDipinjam);
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiPlusKosong);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiPlusPakai);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiPlusRusak);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiMinusKosong);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiMinusPakai);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalAlihKondisiMinusRusak);
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $col++;
            $abjadIncrement++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');
            $objSpreadsheet->getActiveSheet()->getStyle($abjadIncrement . $row)->applyFromArray($this->style_kolom);

            $row++;
            $tgl_sekarang = date($this->FORMAT_DATE, strtotime($tgl_sekarang.$this->INCREMENT_DAY));
        } while ($tgl_sekarang != $tgl_akhir);

        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $kondisi = [
                'Terpakai',
                'Tidak Terpakai',
                'Rusak',
            ];
            
            $row = $row - count($kondisi);

            $rusak = 0;
            $materialTrans = MaterialTrans::where('tipe', 1)
                ->where('status_produk', 2)
                ->where('id_material', $value->id_material)
                ->sum('jumlah');

            if ($materialTrans) {
                $rusak += $materialTrans;
            }
            
            $row--;
            $abjad = 'A';
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Mutasi Pallet');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    // public function generateExcelMutasiPallet($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir, $preview)
    // {
    //     $objSpreadsheet = new Spreadsheet();

    //     $sheetIndex = 0;

    //     //start: style
    //     $style_note = array(
    //         'font' => array(
    //             'bold' => true
    //         )
    //     );
    //     $style_judul_kolom = array(
    //         'fill' => array(
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'color' => array('rgb' => 'D3D3D3')
    //         ),
    //         'font' => array(
    //             'bold' => true
    //         ),
    //         'borders' => array(
    //             'allBorders' => array(
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         )
    //     );
    //     $style_acara = array(
    //         'font' => array(
    //             'size' => 14,
    //             'bold' => true
    //         ),
    //         'alignment' => array(
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         )
    //     );
    //     $style_title = array(
    //         'font' => array(
    //             'bold' => true
    //         ),
    //         'alignment' => array(
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         )
    //     );
    //     $style_isi_kolom = array(

    //         'borders' => array(
    //             'allBorders' => array(
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             )
    //         )
    //     );
    //     $style_ontop = array(
    //         'alignment' => array(
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
    //         )
    //     );
    //     $style_center = array(
    //         'borders' => array(
    //             'allBorders' => array(
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         )
    //     );
    //     $style_kolom = array(
    //         'borders' => array(
    //             'allBorders' => array(
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             )
    //         ),
    //     );
    //     $style_no['alignment'] = array(
    //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //     );
    //     $style_vertical_center['alignment'] = array(
    //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //     );
    //     //end: style

    //     // start : sheet
    //     $objSpreadsheet->createSheet($sheetIndex);
    //     $objSpreadsheet->setActiveSheetIndex($sheetIndex);
    //     // start : title
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

    //     $abjadTitle = 'E';
        
    //     //start : incremental alphabet for adjustment horizontal center
    //     foreach ($gudang as $key) {
    //         $abjadTitle++;
    //     }

    //     foreach ($gudang as $key) {
    //         $abjadTitle++;
    //     }

    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     $abjadTitle++;
    //     //end : incremental alphabet for adjustment horizontal center

    //     $col = 1;
    //     $row = 1;
    //     $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
    //     $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Mutasi Pallet');
    //     $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        
    //     $row++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
    //     $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

    //     $col = 1;
    //     $row++;

    //     $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
    //     $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);
    //     // end : title
    //     // start : judul kolom
    //     $col = 1;
    //     $row = 5;
    //     $abjadOri = 'A';
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

    //     $abjadOri++;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang');
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

    //     $abjadOri++;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Pallet');
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

    //     $abjadOri++;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kondisi Pallet');
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));
        
    //     $abjadOri++;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadOri . ($row + 1));

    //     $abjadOri++;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

    //     $abjadPemasukan = $abjadOri;
    //     $i = 0;
    //     $row = 6;
    //     //start : set nama gudang untuk pemasukan
    //     foreach ($gudang as $key) {
    //         $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
    //         $i++;
    //         $col++;
    //         $abjadPemasukan++;
    //     }
    //     //end : set nama gudang untuk pemasukan
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pemasukan
    //     $row = 5;
    //     $abjadPemasukan--;
    //     $col++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':' . $abjadPemasukan . $row);
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pengeluaran');


    //     $i = 0;
    //     $row = 6;
    //     $abjadPengeluaran = $abjadPemasukan;
    //     //start : nama gudang untuk pengeluaran
    //     foreach ($gudang as $key) {
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
    //         $i++;
    //         $col++;
    //         $abjadPengeluaran++;
    //         $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
    //     }
    //      //end : nama gudang untuk pengeluaran
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pengeluaran
    //     $col++;
    //     $abjadPengeluaran++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row - 1) . ':' . $abjadPengeluaran . ($row - 1));
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row - 1), 'Penyusutan');

    //     $i = 0;
    //     $row = 6;
    //     $abjadPemasukan = $abjadPengeluaran;
    //     $yayasan = Yayasan::all();
    //     //start : daftar nama yayasan
    //     foreach ($yayasan as $key) {
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
    //         $i++;
    //         $col++;
    //         $abjadPengeluaran++;
    //         $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
    //     }
    //     //end : daftar nama yayasan
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total'); //total pengeluaran
    //     $col++;
    //     $abjadPengeluaran++;
    //     $abjadPemasukan++;

    //     $row = 5;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dipinjam');
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row+1), 'Peminjam');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . $row);

    //     $col++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Dikembalikan');
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, ($row + 1), 'Peminjam');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . $row);

    //     $col++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peralihan Kondisi Bertambah');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));

    //     $col++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peralihan Kondisi Berkurang');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));
        
    //     $col++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Status');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row+1));
        
    //     $col++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
    //     $abjadPengeluaran++;
    //     $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row + 1));
    //     $abjad = 'A';

    //     $abjadPemasukanSpes = $abjadPemasukan;
    //     $abjadPemasukanSpes++;
    //     $abjadPemasukan++;
    //     $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukanSpes)->setAutoSize(true);
        
    //     $row = 5;
    //     $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . ($row + 1))->applyFromArray($style_judul_kolom);
    //     $row = 6;
    //     // end : judul kolom

    //     // start : isi kolom
    //     $no = 0;
    //     foreach ($res as $value) {
    //         $no++;
    //         $col = 1;
    //         $row++;

    //         $kondisi = [
    //             'Terpakai',
    //             'Tidak Terpakai',
    //             'Rusak',
    //         ];

    //         $jumlahMerge = count($kondisi)-1; //jumlah berapa baris yang akan digunakan untuk merge pada kolom gudang dan jenis pallet

    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadPengeluaran . $row)->applyFromArray($style_kolom);
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
    //         $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

    //         $col++;
    //         $abjad = chr(ord($abjad) + 1);
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->gudang->nama); //nama gudang
    //         $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_vertical_center);

    //         $col++;
    //         $abjad = chr(ord($abjad) + 1);
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama); //nama pallet
    //         $objSpreadsheet->getActiveSheet()->mergeCells($abjad . $row . ':' . $abjad . ($row + $jumlahMerge));
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . ($row + $jumlahMerge))->applyFromArray($style_kolom);
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_vertical_center);

    //         $col++;
    //         $abjad++;
    //         for ($i=0; $i<count($kondisi); $i++) {
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $kondisi[$i]); //kondisi pallet
    //             $row++;
    //         }

    //         $col++;
    //         //stok awal
    //         $row = $row-count($kondisi);
    //         $stokAkhir[0] = 0;
    //         $stokAkhir[1] = 0;
    //         $stokAkhir[2] = 0;
    //         $stokAkhir[3] = 0;
    //         $abjad++;
    //         for ($i = 0; $i < count($kondisi); $i++) {
    //             $masuk      = MaterialTrans::
    //             leftJoin('aktivitas_harian', function($join) use ($tgl_awal, $value){
    //                 $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
    //                 ->where('draft', 0)
    //                 ->where('aktivitas_harian.id_gudang', $value->id_gudang)
    //                 ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')))
    //                 ;
    //             })
    //             ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
    //                 $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
    //                     ->where('material_adjustment.id_gudang', $value->id_gudang)
    //                     ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
    //             })
    //             ->leftJoin('gudang_stok', function ($join) {
    //                 $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
    //             })
    //             ->where('material_trans.id_material', $value->id_material)
    //             ->where('tipe', 2)
    //             ->where('gudang_stok.id_gudang', $value->id_gudang)
    //             ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
    //             ->sum('material_trans.jumlah');

    //             $keluar     = MaterialTrans::
    //             leftJoin('aktivitas_harian', function($join) use($tgl_awal, $value){
    //                 $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
    //                     ->where('draft', 0)
    //                     ->where('aktivitas_harian.id_gudang', $value->id_gudang)
    //                     ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
    //             })
    //             ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
    //                 $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
    //                     ->where('material_adjustment.id_gudang', $value->id_gudang)
    //                     ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
    //             })
    //             ->leftJoin('gudang_stok', function ($join){
    //                 $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
    //             })
    //             ->where('material_trans.id_material', $value->id_material)
    //             ->where('tipe', 1)
    //             ->where('gudang_stok.id_gudang', $value->id_gudang)
    //             ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
    //             ->sum('material_trans.jumlah');
    //             $saldoAwal  = $masuk - $keluar;
                
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $saldoAwal); //jumlah stok pallet per kondisi
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //             $stokAwal[$i] = $saldoAwal;
    //             $stokAkhir[$i] = $saldoAwal;
    //             $row++;
    //         }
            
    //         $col++;

    //         $tempPenambahan[0] = 0;
    //         $tempPenambahan[1] = 0;
    //         $tempPenambahan[2] = 0;
    //         $tempPenambahan[3] = 0;
    //         foreach ($gudang as $item) {
    //             $row = $row - count($kondisi);
    //             $abjad++;
    //             for ($i = 0; $i < count($kondisi); $i++) {
    //                 $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
    //                     $query->where(function($query) use($item, $value) {
    //                         $query->where('id_gudang', $item->id);
    //                         $query->where('id_gudang_tujuan', $value->id_gudang);
    //                     });
    //                     $query->where('draft', 0);
    //                 })
    //                 ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
    //                 ->where('tipe', 1)
    //                 ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
    //                 ->where('id_material', $value->id_material)
    //                 ->sum('jumlah');
    //                 // $stokAkhir[$i] += $materialTrans;
    //                 $tempPenambahan[$i] = $tempPenambahan[$i]+$materialTrans;
    //                 $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah pallet bertambah per gudang per kondisi
    //                 $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //                 $row++;
    //             }
    //             $col++;
    //         }
    //         $abjadPemasukan++;
    //         $row = $row - count($kondisi);
    //         $abjad++;
    //         for ($i = 0; $i < count($kondisi); $i++) {
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPenambahan[$i]); //total pallet bertambah per kondisi
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //             $row++;
    //         }
            
    //         $col++;
    //         $tempPengeluaran[0] = 0;
    //         $tempPengeluaran[1] = 0;
    //         $tempPengeluaran[2] = 0;
    //         $tempPengeluaran[3] = 0;
    //         foreach ($gudang as $item) {
    //             $row = $row - count($kondisi);
    //             $abjad++;
    //             for ($i = 0; $i < count($kondisi); $i++) {
    //                 $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
    //                     $query->where(function ($query) use ($item, $value) {
    //                         $query->where('id_gudang', $item->id);
    //                         $query->where('id_gudang_tujuan', $value->id_gudang);
    //                     });
    //                     $query->where('draft', 0);
    //                 })
    //                     ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
    //                     ->where('tipe', 2)
    //                     ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
    //                     ->where('id_material', $value->id_material)
    //                     ->sum('jumlah');
    //                 // $stokAkhir[$i] -= $materialTrans;
    //                 $tempPengeluaran[$i] += $materialTrans;
    //                 $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah pallet berkurang per gudang per kondisi
    //                 $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //                 $row++;
    //             }
    //             $col++;
    //         }
    //         $row = $row - count($kondisi);
    //         $abjadPemasukan++;
    //         $abjad++;
    //         for ($i = 0; $i < count($kondisi); $i++) {
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPengeluaran[$i]); //total pengeluaran per kondisi
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //             $row++;
    //         }

    //         $col++;
    //         $tempPenyusutan[0] = 0;
    //         $tempPenyusutan[1] = 0;
    //         $tempPenyusutan[2] = 0;
    //         $tempPenyusutan[3] = 0;
    //         foreach ($yayasan as $item) {
    //             $row = $row - count($kondisi);
    //             $abjad++;
    //             for ($i = 0; $i < count($kondisi); $i++) {
    //                 $materialTrans = MaterialTrans::whereHas('aktivitasHarian', function ($query) use ($item, $value) {
    //                     $query->where('id_gudang', $value->gudang->id);
    //                     $query->where('id_yayasan', $item->id);
    //                 })
    //                     ->where('status_pallet', ($i + 2)) //harus + 2 step agar cocok dengan status pada databse
    //                     ->where('tipe', 1)
    //                     ->where('created_at', '>=', date('Y-m-d', strtotime($tgl_awal)))
    //                     ->where('created_at', '<=', date('Y-m-d', strtotime($tgl_akhir)))
    //                     ->where('id_material', $value->id_material)
    //                     ->sum('jumlah');
    //                 // $stokAkhir[$i] -= $materialTrans;
    //                 $tempPenyusutan[$i] += $materialTrans;
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //                 $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans); //jumlah penyusutan per gudang per kondisi
    //                 $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //                 $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //                 $row++;
    //             }
    //             $col++;
    //         }
    //         $row = $row - count($kondisi);
    //         $abjadPemasukan++;
    //         $abjad++;
    //         for ($i = 0; $i < count($kondisi); $i++) {
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tempPenyusutan[$i]); //total yayasan
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
    //             $row++;
    //         }
    //         $rusak = 0;
    //         $materialTrans = MaterialTrans::where('tipe', 1)
    //             ->where('status_produk', 2)
    //             ->where('id_material', $value->id_material)
    //             ->sum('jumlah');

    //         if ($materialTrans) {
    //             $rusak += $materialTrans;
    //         }

    //         $row = $row - count($kondisi);

    //         $tempPeralihanTambah[0] = 0; 
    //         $tempPeralihanTambah[1] = 0; 
    //         $tempPeralihanTambah[2] = 0; 

    //         $tempPeralihanKurang[0] = 0; 
    //         $tempPeralihanKurang[1] = 0; 
    //         $tempPeralihanKurang[2] = 0; 
            
    //         for ($i = 0; $i < count($kondisi); $i++) {
    //             $abjadDalam = $abjad;
    //             $dipinjam = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
    //                 $query->whereNotNull('peminjaman');
    //             })
    //                 ->where('tipe', 1)
    //                 ->where('status_produk', ($i + 2))
    //                 ->where('id_material', $value->id_material)
    //                 ->sum('jumlah');
    //             // $stokAkhir[$i] -= $dipinjam;
    //             $col++;

    //             $abjadDalam++;
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dipinjam);
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row)->applyFromArray($style_no);
               
    //             $dikembalikan = MaterialTrans::with('aktivitasHarian.aktivitas')->whereHas('aktivitasHarian.aktivitas', function ($query) use ($item) {
    //                 $query->whereNotNull('peminjaman');
    //             })
    //                 ->where('tipe', 2)
    //                 ->where('status_produk', ($i + 2))
    //                 ->where('id_material', $value->id_material)
    //                 ->sum('jumlah');
    //             // $stokAkhir[$i] += $dikembalikan;
    //             $col++;
    //             $abjadDalam++;
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $dikembalikan);
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row)->applyFromArray($style_no);

    //             //start : transaksi masuk
    //             $peralihanTambah = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
    //                 $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
    //                     ->where('draft', 0)
    //                     ->where('aktivitas_harian.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->leftJoin('material_adjustment', function($join) use($value){
    //                     $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
    //                     ->where('material_adjustment.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->leftJoin('gudang_stok', function($join) use($value) {
    //                     $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
    //                     ->where('gudang_stok.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->where(function($query) use($tgl_awal, $tgl_akhir){
    //                     $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
    //                     $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
    //                     $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir) {
    //                         $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
    //                         $query->whereNull('material_trans.id_aktivitas_harian');
    //                         $query->whereNull('material_trans.id_adjustment');
    //                     });
    //                 })
    //                 ->where('tipe', 2)
    //                 ->where('gudang_stok.id_gudang', $value->id_gudang)
    //                 ->where('status_pallet', ($i + 2))
    //                 ->where('material_trans.id_material', $value->id_material)
    //                 ->sum('material_trans.jumlah');
                
    //             $tempPeralihanTambah[$i] = $peralihanTambah;
    //             $stokAkhir[$i] += $peralihanTambah;
    //             //end : transaksi masuk

    //             $col++;
    //             $abjadDalam++;
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanTambah);
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row)->applyFromArray($style_no);

    //             //start : transaksi keluar
    //             $peralihanKurang = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
    //                 $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
    //                     ->where('draft', 0)
    //                     ->where('aktivitas_harian.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->leftJoin('material_adjustment', function($join) use($value) {
    //                     $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
    //                     ->where('material_adjustment.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->leftJoin('gudang_stok', function($join) use($value) {
    //                     $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
    //                     ->where('gudang_stok.id_gudang', $value->id_gudang)
    //                     ;
    //                 })
    //                 ->where(function($query) use($tgl_awal, $tgl_akhir){
    //                     $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
    //                     $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
    //                     $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir) {
    //                         $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
    //                         $query->whereNull('material_trans.id_aktivitas_harian');
    //                         $query->whereNull('material_trans.id_adjustment');
    //                     });
    //                 })
    //                 ->where('tipe', 1)
    //                 ->where('gudang_stok.id_gudang', $value->id_gudang)
    //                 ->where('status_pallet', ($i + 2))
    //                 ->where('material_trans.id_material', $value->id_material)
    //                 ->sum('material_trans.jumlah');
                
    //             $tempPeralihanKurang[$i] = $peralihanKurang;
    //             $stokAkhir[$i] -= $peralihanKurang;
    //             //end : transaksi keluar

    //             $col++;
    //             $abjadDalam++;
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $peralihanKurang);
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row . ":" . $abjadDalam . $row)->applyFromArray($style_kolom);
    //             $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . $row)->applyFromArray($style_no);

    //             $col++;
    //             $abjadDalam++;

    //             $col++;
    //             $abjadDalam++;
    //             $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stokAkhir[$i]);
    //             $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
    //             $row++;
    //             $col -= 6;
    //         }

    //         $abjadDalam--;

    //         // $objSpreadsheet->getActiveSheet()->mergeCells($abjadDalam . ($row-3) . ':'.$abjadDalam . ($row-1)); //merge untuk kolom stok akhir
    //         $status = 'CEK LAGI';

    //         $totalPeralihanBertambah = ($tempPeralihanTambah[0]+$tempPeralihanTambah[1]+$tempPeralihanTambah[2]);
    //         $totalPeralihanBerkurang = ($tempPeralihanKurang[0]+$tempPeralihanKurang[1]+$tempPeralihanKurang[2]);
    //         if ($totalPeralihanBertambah == $totalPeralihanBerkurang) {
    //             $status = 'BALANCE';
    //         }

    //         $objSpreadsheet->getActiveSheet()->mergeCellsByColumnAndRow(($col+5),($row-3),($col+5),($row-1));
    //         $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(($col+5), ($row-3), $status);
    //         $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow(($col+5),($row-3),($col+5),($row-1))->applyFromArray($style_center);
    //         $objSpreadsheet->getActiveSheet()->getStyle($abjadDalam . ($row-3) . ":" . $abjadDalam . ($row-1))->applyFromArray($style_center);

    //         $row--;
    //         $abjad = 'A';
    //     }

    //     //Sheet Title
    //     $objSpreadsheet->getActiveSheet()->setTitle('Laporan Mutasi Pallet');
    //     // end : isi kolom
    //     // end : sheet

    //     #### END : SHEET SESI ####
    //     if ($preview == true) {
    //         $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
    //         echo $writer->generateHTMLHeader();
    //         echo $writer->generateStyles(true);
    //         echo $writer->generateSheetData();
    //         echo $writer->generateHTMLFooter();
    //     } else {
    //         $writer = new Xlsx($objSpreadsheet);
    //         header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    //         header("Cache-Control: no-store, no-cache, must-revalidate");
    //         header("Cache-Control: post-check=0, pre-check=0", false);
    //         header("Pragma: no-cache");
    //         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //         header('Content-Disposition: attachment; filename="' . $nama_file . '"');
    //         $writer->save("php://output");
    //     }
    // }

    public function laporanRealisasi()
    {
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }

        $data['gudang']     = $gudang->get();
        $data['produk']     = Material::produk()->get();
        $data['shift']      = ShiftKerja::orderBy('nama', 'asc')->get();
        $data['aktivitas']  = Aktivitas::nonPenerimaanGi()->get();
        return view('report.realisasi.grid', $data);
    }

    public function realisasi()
    {
        $validator = Validator::make(
            request()->all(),[
            'tgl_awal' => 'required|before_or_equal:tgl_akhir',
            'tgl_akhir' => 'required|after_or_equal:tgl_awal',
        ],[
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute harus lebih dari atau sama dengan :date!',
            'before_or_equal' => ':attribute harus kurang dari atau sama dengan :date!',
        ],[
            'tgl_awal' => 'Tanggal Awal',
            'tgl_akhir' => 'Tanggal Akhir',
        ]);

        if ($validator->fails()) {
            return redirect('report/laporan-realisasi')
                ->withErrors($validator)
                ->withInput();
        }

        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $shift              = request()->input('shift'); //multi
        $kegiatan           = request()->input('kegiatan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = DB::table('aktivitas_harian')->select(
            'aktivitas.nama',
            'aktivitas_harian.updated_at as tanggal',
            'g.nama as nama_gudang', 
            'id_shift',
            'm.nama as nama_material',
            'mt.jumlah'
            )
            ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->leftJoin('gudang as g', 'g.id', '=', 'aktivitas_harian.id_gudang')
            ->leftJoin('material_trans as mt', 'mt.id_aktivitas_harian', '=', 'aktivitas_harian.id')
            ->leftJoin('material as m', 'm.id', '=', 'id_material')
            ->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir])
            ->where('draft', 0)
            ->whereNull('peminjaman')
            ->whereNull('penerimaan_gi')
            ->where('kategori', 1)
            ->orderBy('aktivitas_harian.updated_at', 'asc')
            ;
        
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
        }

        $res = $res->get();

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_realisasi.xlsx';
        $this->generateExcelRealisasi($res, $nama_file, $kegiatan, $shift, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelRealisasi($res, $nama_file, $kegiatan, $shift, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );
        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
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

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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
            $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_no);
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
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle("G" . $row)->applyFromArray($style_no);

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
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanKeluhanGp()
    {
        $data['title']      = 'Laporan Keluhan GP';
        $data['gudang']     = Gudang::gp()->get();
        // $data['keluhan']    = Keluhan::all();
        $data['aktivitas']  = Aktivitas::whereNotNull('pengiriman')->get();
        $data['produk']     = Material::produk()->get();
        return view('report.keluhan-gp.grid', $data);
    }

    public function keluhanGp()
    {
        $gudang             = request()->input('gudang'); //multi
        $produk             = request()->input('produk');
        $pilih_produk       = request()->input('pilih_produk'); //multi
        $kegiatan           = request()->input('kegiatan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));
        $res = AktivitasKeluhanGp::select(
            'aktivitas_keluhan_gp.*',
            'g.nama as nama_gudang',
            'm.nama as nama_material',
            'ah.updated_at as tanggal'
            )
            ->leftJoin('aktivitas_harian as ah', 'aktivitas_keluhan_gp.id_aktivitas_harian', '=', 'ah.id')
            ->leftJoin('material as m', 'm.id', '=', 'aktivitas_keluhan_gp.id_material')
            ->leftJoin('gudang as g', 'g.id', '=', 'ah.id_gudang')
            ->whereBetween('ah.updated_at', [$tgl_awal, $tgl_akhir])
            ->where(function ($query) use ($pilih_produk, $produk) {
                if ($produk == 2) {
                    foreach ($pilih_produk as $key => $value) {
                        $query->orWhere('m.id', $value);
                    }
                }
            })
            ;

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $res = $res->where('g.id', $localGudang->id);
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
            $res = $res->where('ah.id_aktivitas', $kegiatan[0]);
            foreach ($kegiatan as $key => $value) {
                $res = $res->orWhere('ah.id_aktivitas', $value);
            }
        }

        $res = $res->orderBy('ah.updated_at')->get();

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_keluhan_gp.xlsx';
        $this->generateExcelKeluhanGp($res, $nama_file, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelKeluhanGp($res, $nama_file, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: styles
        $style_title = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
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
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Keluhan GP');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Aktivitas '.date('d/m/Y', strtotime($tgl_awal)).' - '. date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_no);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gudang Penyangga');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Keluhan');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jenis Pupuk');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Kuantum');

        $abjad = 'A';
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row . ":" . 'F' . $row)->applyFromArray($style_judul_kolom);
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
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($value->jumlah, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
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
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanTransaksiMaterial()
    {
        $data['title'] = 'Laporan Transaksi Material';
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        
        $data['gudang'] = $gudang->get();
        $data['produk'] = Material::produk()->get();
        return view('report.transaksi-material.grid', $data);
    }

    public function transaksiMaterial()
    {
        $validator = Validator::make(
            request()->all(),[
            'tgl_awal' => 'required|before_or_equal:tgl_akhir',
            'tgl_akhir' => 'required|after_or_equal:tgl_awal',
        ],[
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute harus lebih dari atau sama dengan :date!',
            'before_or_equal' => ':attribute harus kurang dari atau sama dengan :date!',
        ],[
            'gudang' => 'Gudang',
            'tgl_awal' => 'Tanggal Awal',
            'tgl_akhir' => 'Tanggal Akhir',
        ]);

        if ($validator->fails()) {
            return redirect('report/laporan-transaksi-material')
                ->withErrors($validator)
                ->withInput();
        }

        $gudang             = request()->input('gudang'); //multi
        $material           = request()->input('material');
        $pilih_material     = request()->input('pilih_material'); //multi
        $tgl_awal           = request()->input('tgl_awal') == null? '' : date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = request()->input('tgl_akhir') == null ? '' : date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = MaterialTrans::select('material_trans.*')->with('aktivitasHarian', 'aktivitasHarian.gudang', 'aktivitasHarian.gudangTujuan')
        ->with('material')
        ->leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
        ->leftJoin('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
        ->whereHas('material', function($query) {
            $query->where('kategori', 1);
        })
        ->whereHas('aktivitasHarian', function($query) {
            $query->where('draft', 0);
        })
        ->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir])
        ->orderBy('gudang.nama', 'asc')
        ;

        if ($gudang != null) {
            $res = $res->whereHas('aktivitasHarian', function ($query) use ($gudang) {
                $query = $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id_gudang', $value);
                }
            });

            $resGudang = Gudang::internal()->where(function ($query) use ($gudang) {
                $query->where('id', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query = $query->orWhere('id', $value);
                }
            })->get();
        } else {
            $resGudang = Gudang::internal()->get();
        }

        if ($material == 2) {
            $res = $res->where(function ($query) use ($pilih_material) {
                foreach ($pilih_material as $key => $value) {
                    $query = $query->orWhere('id_material', $value);
                }
            });

            $produk = Material::produk()->where(function($query) use($pilih_material){
                $query->where('id', $pilih_material[0]);
                foreach ($pilih_material as $key => $value) {
                    $query = $query->orWhere('id', $value);
                }
            })->get();
        } else {
            $res = $res->whereHas('material', function ($query) {
                $query = $query->where('kategori', 1);
            });

            $produk = Material::produk()->get();
        }

        $res = $res->get();

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        if (!is_dir(storage_path() . '/app/public/excel/')) {
            mkdir(storage_path() . '/app/public/excel', 755);
        }

        $nama_file = date("YmdHis") . '_transaksi_material.xlsx';
        $this->generateExcelTransaksiMaterial($res, $nama_file, $produk, $resGudang, $tgl_awal, $tgl_akhir, $preview);
    }

    private function getKuantum($result)
    {
        $kuantums = '';
        $kuantum = '';

        foreach ($result->materialTrans as $key) {
            if ($key->material->kategori == 1){
                if ($kuantums == '') {
                    $kuantums = $key->material->nama;
                } else {
                    $kuantums = $kuantums.', '. $key->material->nama;
                }

                if ($kuantum == '') {
                    if ($key->tipe == 1) {
                        $kuantum = '-' . round($key->jumlah, 3);
                    } else {
                        $kuantum = round($key->jumlah, 3);
                    }
                } else {
                    if ($key->tipe == 1) {
                        $kuantum = $kuantum . ', ' . '-' . round($key->jumlah, 3);
                    } else {
                        $kuantum = $kuantum . ', ' . round($key->jumlah, 3);
                    }
                }
            }
        }

        return $kuantums;
    }

    public function generateExcelTransaksiMaterial($res, $nama_file, $produk, $resGudang, $tgl_awal, $tgl_akhir, $preview)
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
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':I' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Transaksi Material');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':I' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peridode: '.date('d/m/Y', strtotime($tgl_awal)).' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);


        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'SHIFT');
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA TRANSAKSI');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA GUDANG');
        
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA CHECKER');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA PRODUK');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KUANTUM');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TUJUAN');

        $abjad = 'A';
        
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadOri . $row)->applyFromArray($style_judul_kolom);
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        $totalStok = 0;
        $totalRusak = 0;
        $totalNormal = 0;
        $jumlahStok = 0;

        foreach ($produk as $value) {
            $tempRes =  DB::table('material_trans')
            ->leftJoin('aktivitas_harian', function ($join) {
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
            ->where('id_material', $value->id)
            ->where(function ($query) use ($tgl_awal) {
                $query->where('aktivitas_harian.updated_at', '<', $tgl_awal);
                $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
            })
            ;

            foreach ($resGudang as $key) {
                $tempRes = $tempRes->where(function($query) use($key){
                    $query->where('aktivitas_harian.id_gudang', $key->id);
                    $query->orWhere('material_adjustment.id_gudang', $key->id);
                });
            }

            $tempRes = $tempRes->get();
            
            $penambahan = 0;
            $pengurangan = 0;
            foreach ($tempRes as $row2) {
                if ($row2->tipe == 1) {
                    $pengurangan = $pengurangan + $row2->jumlah;
                } else {
                    $penambahan = $penambahan + $row2->jumlah;
                }
            }
    
            $jumlahStok = $jumlahStok + $penambahan - $pengurangan;
        }

        foreach ($res as $value) {
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjad . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y H:i:s', strtotime($value->created_at)));

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Shift '.$value->aktivitasHarian->id_shift);
            
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($value->aktivitasHarian->aktivitas != null)?$value->aktivitasHarian->aktivitas->nama:'-');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudang))?$value->aktivitasHarian->gudang->nama:'');
            
            $col++;
            $user = Users::find($value->aktivitasHarian->updated_by);
            $tenaga_kerja = TenagaKerjaNonOrganik::find($user->id);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $tenaga_kerja->nama);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);

            $col++;
            $objSpreadsheet->getActiveSheet()->getStyle('H' . $row)->applyFromArray($style_no);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->tipe == 1 ? '-'. round($value->jumlah, 3) : round($value->jumlah, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $col++;
            if ($value->aktivitasHarian->so == null) {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudangTujuan)) ? $value->aktivitasHarian->gudangTujuan->nama:'');
            } else {
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->so)) ? $value->aktivitasHarian->so : '');
            }
            
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

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
        }
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . 5 . ":" . $abjadOri . $row)->applyFromArray($style_kolom);
        $totalStok = $totalStok + $jumlahStok;
        $totalNormal = $totalStok-$totalRusak;
        
        $row++;
        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Stok');    
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalStok, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_no);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Rusak');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalRusak, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_no);

        $row++;
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Normal');
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalNormal, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_judul_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_no);

        $abjad2 = chr(ord($abjad) + 1);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . ($row - 2) . ":" . $abjad2 . $row)->applyFromArray($style_kolom);
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Transaksi Material");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanStok()
    {
        $data['title'] = 'Laporan Stok';

        $gudang = new Gudang;

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }

        $data['gudang'] = $gudang->get();
        $data['produk'] = Material::produk()->get();
        return view('report.stok.grid', $data);
    }

    public function stok()
    {
        $gudang     = request()->input('gudang'); //multi
        $tipe_produk= request()->input('produk');
        $produk     = request()->input('pilih_produk'); //multi
        $tgl        = (request()->input('tgl_awal') == '') ? date('Y-m-d') : (request()->input('tgl_awal'));
        $tgl        = date('Y-m-d', strtotime($tgl));

        $res        = [];
        $area       = new Area;
        $resProduk  = $area->getProduk($gudang, $tipe_produk, $produk, $tgl);
        $resArea    = $area->getStokGudang($gudang, $tipe_produk, $produk, $tgl);
        $nama_file  = date("YmdHis") . '_posisi_stok.xlsx';

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $this->generateExcelStok($res, $nama_file, $resProduk, $resArea, $tgl, $preview);
    }

    public function generateExcelStok($res, $nama_file, $produk, $area, $tgl_awal, $preview)
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
            ),
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
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98D6EA')
            ),
            'font' => array(
                'bold' => true
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
        // $jumlahProduk = count($produk);
        $col = 1;
        $row = 1;
        $abjadTitle = 'C';

        foreach ($produk as $key) {
            $abjadTitle++;
        }
        $abjadTitle++;
        
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'POSISI STOK GUDANG GRESIK');
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal: '.date('d/m/Y', strtotime($tgl_awal)));
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

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
        $listProduk = [];
        foreach ($produk as $key) {
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $listProduk[$key->id_material] = $col;
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

        //coding transaksi di sini
        $id_gudang = null;
        $id_material = null;
        $kapasitas = 0;
        $total_per_gudang = 0;
        $total_keseluruhan = 0;
        $total_kapasitas = 0;
        $total_per_produk = [];
        foreach($area as $dArea){
            $abjadProduk = 'D';
            if($dArea->id_gudang != $id_gudang){
                $no++;
                $row++;
                $id_gudang = $dArea->id_gudang;
                $kapasitas = $dArea->kapasitas;
                $total_per_gudang = $dArea->total;
            } else {
                $kapasitas = $kapasitas + $dArea->kapasitas;
                $total_per_gudang = $total_per_gudang + $dArea->total;
            }
            if(isset($total_per_produk[$dArea->id_material]))
                $total_per_produk[$dArea->id_material] = $total_per_produk[$dArea->id_material] + $dArea->total;
            else
                $total_per_produk[$dArea->id_material] = $dArea->total;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $no); //nomor
            $objSpreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray($style_no);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $dArea->nama_gudang); //nama gudang
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $kapasitas); //nama area
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');

            $objSpreadsheet->getActiveSheet()->getStyle($abjadProduk . $row)->applyFromArray($style_no);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($listProduk[$dArea->id_material], $row, round($dArea->total, 3)); //nama area
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($listProduk[$dArea->id_material], $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $abjadProduk++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadProduk . $row)->applyFromArray($style_no);

            $abjadProduk++;
            $objSpreadsheet->getActiveSheet()->getStyle($abjadProduk . $row)->applyFromArray($style_no);
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($total_per_gudang, 3)); //nama area
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            
            $total_keseluruhan = $total_keseluruhan + $dArea->total;
            $total_kapasitas = $total_kapasitas + $dArea->kapasitas;
        }
        $objSpreadsheet->getActiveSheet()->getStyle("A7:" . $abjadPemasukan . ($row))->applyFromArray($style_isi_kolom);

        $row++;
        $objSpreadsheet->getActiveSheet()->getStyle("A{$row}:" . $abjadPemasukan . ($row))->applyFromArray($style_kolom);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $row, 'Jumlah');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $row, round($total_kapasitas, 3)); //kapasitas
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($total_keseluruhan, 3)); //jumlah produk
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        
        foreach ($produk as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($listProduk[$key->id_material], $row, round($total_per_produk[$key->id_material], 3)); //jumlah produk
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($listProduk[$key->id_material], $row)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $objSpreadsheet->getActiveSheet()->getStyle("B{$row}:{$abjadPemasukan}{$row}")->applyFromArray($style_title);
        
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Posisi Stok');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
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
            // $query->whereBetween('ah.updated_at', [$tgl_awal, $tgl_akhir]);
            // ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')))
            $query->whereBetween(DB::raw("TO_CHAR(ah.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
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

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_mutasi_stok.xlsx';
        $this->generateExcelMutasiStok($res->get(), $nama_file, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelMutasiStok($res, $nama_file, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
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
        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $col = 1;
        $row = 1;
        $gudang = Gudang::internal()->get();
        $abjadTitle = 'C';

        foreach ($gudang as $key) {
            $abjadTitle++;
        }

        foreach ($gudang as $key) {
            $abjadTitle++;
        }

        $abjadTitle++;

        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Harian Mutasi Stock Gudang Gresik I & II');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Departemen Distribusi Wilayah I');
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':'.$abjadTitle . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Tanggal ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 5;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'No');

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Produk');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Awal');
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadOri . $row . ':'. $abjadOri . ($row + 1));

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Pemasukan');

        
        $abjadPemasukan = $abjadOri;
        $i = 0;
        $row = 6;
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
        foreach ($gudang as $key) {
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $key->nama);
            $i++;
            $col++;
            $abjadPemasukan++;
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPemasukan)->setAutoSize(true);
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
            $i++;
            $col++;
            $abjadPengeluaran++;
            $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
        }

        $row = 5;
        $abjadPemasukan = chr(ord($abjadPemasukan) + 1);
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . $row . ':' . $abjadPengeluaran . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Stok Akhir');
        

        
        $abjadPengeluaran++;
        $objSpreadsheet->getActiveSheet()->getColumnDimension($abjadPengeluaran)->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPengeluaran . $row . ':' . $abjadPengeluaran . ($row+1));
        $abjad = 'A';

        
        $row = 5;
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadPengeluaran . ($row+1))->applyFromArray($style_judul_kolom);
        $row = 6;
        // end : judul kolom

        // start : isi kolom
        $no = 0;
        foreach ($res as $value) {
            $abjad = 'A';
            $no++;
            $col = 1;
            $row++;

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":". $abjadPengeluaran . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no); //nomor

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama); //nama

            $materialTransMengurang = MaterialTrans::
                leftJoin('aktivitas_harian', function ($join) use ($tgl_awal) {
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                })
                ->leftJoin('aktivitas', function ($join) {
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        // ->whereNotNull('status_aktivitas')
                        ;
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal) {
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                })
                ->where('tipe', 1)
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');

            $materialTransMenambah = MaterialTrans::
                leftJoin('aktivitas_harian', function ($join) use ($tgl_awal) {
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                })
                ->leftJoin('aktivitas', function ($join) {
                    $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        // ->whereNotNull('status_aktivitas')
                        ;
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal) {
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->where('id_material', $value->id_material)
                ->where(function ($query) use ($tgl_awal) {
                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                    $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                })
                ->where('tipe', 2)
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');

            $stokAwal = $materialTransMenambah - $materialTransMengurang;
            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAwal, 3)); //stok awal
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_no);
            $stokAkhir = $stokAwal;
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian as ah', 'ah.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join) {
                    $join->on('aktivitas.id', '=', 'ah.id_aktivitas')
                        // ->whereNotNull('status_aktivitas')
                        ;
                })
                ->leftJoin('material_adjustment as ma', 'ma.id', '=', 'material_trans.id_adjustment')
                ->where('ah.id_gudang', $item->id)
                ->where('tipe', 2)
                ->where(function($query) use($tgl_awal, $tgl_akhir){
                    $query->whereBetween(DB::raw("TO_CHAR(ah.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                    $query->orWhereBetween('ma.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('id_material', $value->id_material)
                ->where('draft', 0)
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');
                
                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3)); //pemasukan per gudang per item
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_no);
            }
            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian as ah', 'ah.id', '=', 'material_trans.id_aktivitas_harian')
                ->leftJoin('aktivitas', function ($join) {
                    $join->on('aktivitas.id', '=', 'ah.id_aktivitas')
                        // ->whereNotNull('status_aktivitas')
                        ;
                })
                ->leftJoin('material_adjustment as ma', 'ma.id', '=', 'material_trans.id_adjustment')
                ->where('ah.id_gudang', $item->id)
                ->where('tipe', 1)
                ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                    $query->whereBetween(DB::raw("TO_CHAR(ah.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                    $query->orWhereBetween('ma.created_at', [$tgl_awal, $tgl_akhir]);
                })
                ->where('id_material', $value->id_material)
                ->where('draft', 0)
                ->whereNotNull('status_aktivitas')
                ->sum('jumlah');

                $stokAkhir -= $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($materialTrans, 3)); //pengeluaran per gudang per item
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':'. $abjadPengeluaran . $row)->applyFromArray($style_no);
            }

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($stokAkhir, 3)); //stok akhir
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Mutasi Stok");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanLogSheet()
    {
        $data['title'] = 'Laporan Log Sheet';
        $data['shift'] = ShiftKerja::get();
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        $data['produk'] = Material::produk()->get();
        return view('report.log-sheet.grid', $data);
    }

    public function logSheet()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'tanggal'       => 'required',
                'gudang'        => 'required',
                'shift'         => 'required',
                'pilih_produk'  => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'pilih_produk'  => 'Produk',
                'tanggal'       => 'Tanggal',
                'shift'         => 'Shift',
                'gudang'        => 'Gudang',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-log-sheet')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang');
        $pilih_produk       = request()->input('pilih_produk');
        $shift              = request()->input('shift');
        $tanggal            = date('Y-m-d', strtotime(request()->input('tanggal')));

        $res = DB::table('area_stok')
        ->distinct()
        ->select(
            'area_stok.id',
            'id_material',
            'id_area',
            'area.nama'
        )
        ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        ->where('id_gudang', $gudang)
        ->where('id_material', $pilih_produk)
        ->whereNull('area.end_date')
        ->orderBy('id_area')
        ->get()
        ->groupBy('id_area')
        ;
        // dd($res);
        $nama_file = date("YmdHis") . '_logsheet.xlsx';

        $resGudang = Gudang::find($gudang);
        $resShift = ShiftKerja::find($shift);
        $resProduk = Material::find($pilih_produk);

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $this->generateExcelLogSheet($res, $nama_file, $tanggal, $resGudang, $resProduk, $resShift, $preview);
    }

    public function generateExcelLogSheet($res, $nama_file, $tanggal, $resGudang, $resProduk, $resShift, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
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
            'font' => array(
                'bold' => true
            ),
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
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $style_no['alignment'] = array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        );

        $style_isi_kolom = array(
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
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
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
        //end: style

        // start : sheet
        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'LOG SHEET AREA PENYIMPANAN PUPUK DI SEKSI '. $resGudang->nama);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'JENIS PUPUK ');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, $row, ': '. $resProduk->nama);
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_ontop);
        
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'HARI / TGL ');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, $row, ': ' . helpDate($tanggal, 'li'));
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_ontop);
        
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'SHIFT ');
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, $row, ': '.$resShift->nama);
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle("B" . $row)->applyFromArray($style_ontop);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AREA');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'STOK AWAL');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PEMASUKAN');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PENGELUARAN');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);
        
        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'STOK AKHIR');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $row = 7;
        
        // end : judul kolom

        // start : isi kolom
       
        $totalStokAwal = 0;
        $totalMasukKeseluruhan = 0;
        $totalKeluarKeseluruhan = 0;
        $totalStokAkhir = 0;
        foreach ($res as $roww) {
            $jumlah =0;
            $jumlahStokAwal = 0;
            $totalMasuk = 0;
            $totalKeluar = 0;
            foreach ($roww as $value) {
                if ($resShift->id == 1) {
                    $stokTanggalSebelum = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                            ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment');
                        })
                        ->where(function ($query) use ($tanggal) {
                            $query->where(function($query) use($tanggal){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 07:00:00')));
                                $query->orWhere(function($query) use($tanggal){
                                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 07:00:00')));
                                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 15:00:00')));
                                    $query->where('id_shift', 3);
                                });
                            });
                            // $query->orWhere(function($query) use($tanggal) {
                            //     $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 07:00:00')));
                            //     $query->where('id_shift', 1);
                            // });
                            $query->orWhere(function ($query) use ($tanggal) {
                                $query->where('material_adjustment.tanggal', '<', $tanggal);
                                $query->orWhere(function($query) use ($tanggal){
                                    $query->where('material_adjustment.tanggal', '=', $tanggal);
                                    $query->where('material_adjustment.shift', '=', 3);
                                });
                            });
                        })
                        ->get();
                } else if ($resShift->id == 2) {
                    $stokTanggalSebelum = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                            ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment');
                        })
                        ->where(function ($query) use ($tanggal) {
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 15:00:00')));
                            $query->orWhere(function($query) use($tanggal){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 15:00:00')));
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 23:00:00')));
                                $query->where('id_shift', 1);
                            });
                            $query->orWhere('material_adjustment.tanggal', '<', $tanggal);
                            $query->orWhere(function ($query) use ($tanggal) {
                                    $query->where('material_adjustment.tanggal', '=', $tanggal);
                                    $query->where(function($query){
                                        $query->where('material_trans.shift_id', 1);
                                        $query->orWhere('material_trans.shift_id', '=', 3);
                                    });
                            });
                        })
                        ->get();
                } else if ($resShift->id == 3) {
                    $stokTanggalSebelum = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join) {
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                            ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join) {
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment');
                        })
                        ->where(function ($query) use ($tanggal) {
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 23:00:00 -1 day')));
                            $query->orWhere(function($query) use($tanggal){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 23:00:00 -1 day')));
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 00:30:00')));
                                $query->where('id_shift', 2);
                            });
                            // $query->orWhere('material_adjustment.tanggal', '<=', date('Y-m-d', strtotime($tanggal . '-1 day')));
                            $query->orWhere('material_adjustment.tanggal', '<=', date('Y-m-d', strtotime($tanggal . '-1 day')));
                            $query->orWhere(function ($query) use ($tanggal) {
                                    $query->where('material_adjustment.tanggal', '=', date('Y-m-d', strtotime($tanggal . '-1 day')));
                                    $query->where(function($query){
                                        $query->where('material_trans.shift_id', 2);
                                        $query->orWhere('material_trans.shift_id', 1);
                                    });
                            });
                        })
                        ->get();
                }

                if ($resShift->id == 3) {
                    $stokTanggalIni = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join) use ($tanggal) {
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 23:00:00 -1 day')))
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 08:30:00')))
                                // ->orWhere(function($query) use($tanggal) {
                                //     $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 07:00:00')));
                                //     $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 22:00:00')));
                                //     $query->where('id_shift', 3);
                                // })
                                ;
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                            ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tanggal, $resShift) {
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                // ->where('material_adjustment.tanggal', $tanggal);
                                ->where('material_adjustment.shift', '=', $resShift->id)
                                ->where(function($query) use ($tanggal){
                                    $query->where('material_adjustment.tanggal', '<', $tanggal);
                                    $query->orWhere('material_adjustment.tanggal', '=', $tanggal);
                                });
                        })
                        ->where(
                            function ($query) use ($resShift) {
                                $query->where('id_shift', $resShift->id);
                                $query->orWhere('shift', $resShift->id);
                            }
                        )
                        ->get();
                } else if ($resShift->id == 1) {
                    $stokTanggalIni = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join) use ($tanggal) {
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 07:00:00')))
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 16:30:00')))
                                // ->orWhere(function($query) use($tanggal) {
                                //     $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 15:00:00')));
                                //     $query->where('id_shift', 1);
                                //     $query->where('draft', 0);
                                // })
                                ;
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                            ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tanggal, $resShift) {
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.tanggal', $tanggal)
                                ->where('material_adjustment.shift', '=', $resShift->id);
                                // ->where(function($query) use ($tanggal){
                                //     $query->where('material_adjustment.tanggal', '<', $tanggal);
                                //     $query->orWhere('material_adjustment.tanggal', '=', $tanggal);
                                // });
                        })
                        ->where(
                            function ($query) use ($resShift) {
                                $query->where('id_shift', $resShift->id);
                                $query->orWhere('shift', $resShift->id);
                            }
                        )
                        ->get();
                } else if ($resShift->id == 2) {
                    $stokTanggalIni = DB::table('material_trans')
                        ->where('material_trans.id_area_stok', $value->id)
                        ->leftJoin('aktivitas_harian', function ($join) use ($tanggal) {
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 15:00:00')))
                                ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tanggal . ' 00:30:00 +1 day')))
                                // ->orWhere(function($query) use($tanggal) {
                                //     $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tanggal . ' 23:00:00')));
                                //     $query->where('id_shift', 2);
                                // })
                                ;
                        })
                        ->leftJoin('aktivitas', function ($join){
                            $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                                ->whereNotNull('status_aktivitas');
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tanggal, $resShift) {
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.tanggal', $tanggal)
                                ->where('material_adjustment.shift', '=', $resShift->id);
                        })
                        ->where(
                            function ($query) use ($resShift) {
                                $query->where('id_shift', $resShift->id);
                                $query->orWhere('shift', $resShift->id);
                            }
                        )
                        ->get();

                }


                $pre_masuk = 0;
                $pre_keluar = 0;
                foreach ($stokTanggalSebelum as $preKey) {
                    if ($preKey->tipe == 2) {
                        $pre_masuk = $pre_masuk + $preKey->jumlah;
                    } else if ($preKey->tipe == 1) {
                        $pre_keluar = $pre_keluar + $preKey->jumlah;
                    }
                }

                $jumlahStokAwal += $pre_masuk - $pre_keluar;

                $masuk = 0;
                $keluar = 0;
                
                foreach ($stokTanggalIni as $singletonKey) {
                    if ($singletonKey->tipe == 2) {
                        $masuk = $masuk + $singletonKey->jumlah;
                    } else if ($singletonKey->tipe == 1) {
                        $keluar = $keluar + $singletonKey->jumlah;
                    }
                }

                $totalMasuk += $masuk;
                $totalKeluar += $keluar;

                $jumlah  += $pre_masuk - $pre_keluar + $masuk - $keluar;

                
            }

            if ($jumlahStokAwal >0 || $totalMasuk > 0 || $totalKeluar > 0) {
                $col = 1;
                $abjad = 'A';
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $roww[0]->nama);
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

                $col++;
                $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($jumlahStokAwal, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

                $col++;
                $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalMasuk, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

                $col++;
                $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalKeluar, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

                $col++;
                $abjad++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($jumlah, 3));
                $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
                $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

                $totalStokAwal += $jumlahStokAwal;
                $totalMasukKeseluruhan += $totalMasuk;
                $totalKeluarKeseluruhan += $totalKeluar;
                $totalStokAkhir += $jumlah;

                $row++;
            }
        }
        $col = 1;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $row, 'Total');
        $objSpreadsheet->getActiveSheet()->getStyle('A'. $row)->applyFromArray($style_isi_kolom);

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalStokAwal, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle('B' . $row)->applyFromArray($style_isi_kolom);

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalMasukKeseluruhan, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle('C' . $row)->applyFromArray($style_isi_kolom);

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalKeluarKeseluruhan, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle('D' . $row)->applyFromArray($style_isi_kolom);
        
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalStokAkhir, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle('E' . $row)->applyFromArray($style_isi_kolom);

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle("Laporan Log Sheet");
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanBiayaAlatBerat()
    {
        $data['title'] = 'Laporan Biaya Alat Berat';
        $data['aktivitas'] = Aktivitas::get();
        $data['jenisAlatBerat'] = KategoriAlatBerat::get();
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        return view('report.biaya-alat-berat.grid', $data);
    }

    public function biayaAlatBerat()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'tgl_awal'  => 'required',
                'tgl_akhir' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'tgl_awal'  => 'Tanggal Awal',
                'tgl_akhir' => 'Tanggal Akhir',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-biaya-alat-berat')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang');
        $jenis_alat_berat   = request()->input('jenis_alat_berat');
        $aktivitas          = request()->input('aktivitas');
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir').'+1 day'));

        $res = DB::table('aktivitas_harian_alat_berat')
            ->distinct()
            ->select(
            'aktivitas_harian.*',
            'alat_berat.id_kategori',
            'aktivitas.nama as nama_aktivitas',
            'aktivitas_harian.updated_at as tanggal_aktivitas',
            'gudang.nama as nama_gudang',
            'alat_berat_kat.nama as nama_kategori',
            DB::raw('(SELECT anggaran FROM aktivitas_alat_berat WHERE aktivitas_alat_berat.id_kategori_alat_berat = alat_berat_kat.id AND aktivitas_alat_berat.id_aktivitas = aktivitas.id LIMIT 1) as anggaran')
            )
            ->leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'aktivitas_harian_alat_berat.id_aktivitas_harian')
            ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->leftJoin('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
            ->leftJoin('alat_berat', 'alat_berat.id', '=', 'aktivitas_harian_alat_berat.id_alat_berat')
            ->leftJoin('alat_berat_kat', 'alat_berat_kat.id', '=', 'alat_berat.id_kategori')
            ->whereBetween(DB::raw("TO_CHAR( aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS' )"), [date('Y-m-d H:i:s', strtotime($tgl_awal.' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir.' 23:00:00 -1 day'))])
            ->whereNotNull('butuh_alat_berat')
            ->latest('aktivitas_harian.updated_at')
            ;

        $nama_file = date("YmdHis") . '_biaya_alat_berat.xlsx';

        $resGudang = Gudang::get();
        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
            $resGudang      = Gudang::whereIn('id', $gudang)->get();
        }

        $resAktivitas = Aktivitas::get();
        if ($aktivitas) {
            $res = $res->where(function ($query) use ($aktivitas) {
                $query->where('id_aktivitas', $aktivitas[0]);
                foreach ($aktivitas as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
            $resAktivitas   = Aktivitas::whereIn('id', $aktivitas)->get();
        }

        $resJenisAlatBerat = KategoriAlatBerat::get();
        if ($jenis_alat_berat) {
            $res = $res->where(function ($query) use ($jenis_alat_berat) {
                $query->where('id_kategori', $jenis_alat_berat[0]);
                foreach ($jenis_alat_berat as $key => $value) {
                    $query->orWhere('id_kategori', $value);
                }
            });
            $resJenisAlatBerat      = KategoriAlatBerat::whereIn('id', $jenis_alat_berat);
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }
        $res = $res->get();

        $this->generateExcelBiayaAlatBerat($res, $nama_file, $resGudang, $resAktivitas, $resJenisAlatBerat, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelBiayaAlatBerat($res, $nama_file, $resGudang, $resAktivitas, $resJenisAlatBerat, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
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
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
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
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
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

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REPORT REALISASI ALAT BERAT');
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PERIODE : ' . date('d/m/Y', strtotime($tgl_awal)).' - '.date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'GUDANG');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AKTIVITAS');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'JENIS ALAT BERAT');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI TONASE ALAT BERAT');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BIAYA Rp/Ton (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI BIAYA ALAT BERAT (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $row = 7;
        // end : judul kolom

        // start : isi kolom
        $no = 1;
        $totalBiaya = 0;
        $totalRealisasi = 0;
        foreach ($res as $value) {
            $col = 1;
            $abjad = 'A';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_aktivitas);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d/m/Y', strtotime($value->tanggal_aktivitas)));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_kategori);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $tonase = MaterialTrans::select('jumlah')->where('id_aktivitas_harian', $value->id)->whereNotNull('status_produk')->get();
            $jumlahTonase = 0;
            foreach ($tonase as $key) {
                $jumlahTonase += $key->jumlah;
            }

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($jumlahTonase, 3));
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->anggaran);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            
            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($jumlahTonase*$value->anggaran));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            $biaya = $jumlahTonase * $value->anggaran;
            $totalBiaya += $biaya;

            $totalRealisasi += $jumlahTonase;

            $row++;
            $no++;
        }
        $col = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

        $col = 1;
        $abjad = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Biaya');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_ontop);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col = 6;
        $abjad = 'F';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalRealisasi, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '-');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalBiaya);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom)->getNumberFormat()->setFormatCode('#,##0');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Biaya Alat Berat');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanBiayaTkbm()
    {
        $data['title'] = 'Laporan Biaya TKBM';
        $data['aktivitas'] = Aktivitas::get();
        $data['jenisAlatBerat'] = KategoriAlatBerat::get();
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        return view('report.biaya-tkbm.grid', $data);
    }

    public function biayaTkbm()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'tgl_awal'  => 'required',
                'tgl_akhir' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'tgl_awal'  => 'Tanggal Awal',
                'tgl_akhir' => 'Tanggal Akhir',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-biaya-tkbm')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang');
        $aktivitas          = request()->input('aktivitas');
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = DB::table('aktivitas_harian')
            ->select(
            'aktivitas_harian.*',
            'aktivitas.anggaran_tkbm',
            'gudang.nama as nama_gudang',
            'aktivitas.nama as nama_aktivitas'
            )
            ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->leftJoin('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
            ->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir])
            ->whereNotNull('butuh_tkbm')
            ->latest('aktivitas_harian.updated_at')
            ;

        $nama_file = date("YmdHis") . '_biaya_tkbm.xlsx';

        $resGudang = Gudang::get();
        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
            $resGudang      = Gudang::whereIn('id', $gudang)->get();
        }

        $resAktivitas = Aktivitas::get();
        if ($aktivitas) {
            $res = $res->where(function ($query) use ($aktivitas) {
                $query->where('id_aktivitas', $aktivitas[0]);
                foreach ($aktivitas as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
            $resAktivitas   = Aktivitas::whereIn('id', $aktivitas)->get();
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }
        $res = $res->get();

        $this->generateExcelBiayaTkbm($res, $nama_file, $resGudang, $resAktivitas, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelBiayaTkbm($res, $nama_file, $resGudang, $resAktivitas, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
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
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $style_ontop = array(
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
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
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
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

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REPORT REALISASI TKBM');
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PERIODE : ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle("A" . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'GUDANG');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AKTIVITAS');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI TONASE MUAT TKBM');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BIAYA Rp/Ton (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI BIAYA TKBM (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $row = 7;
        // end : judul kolom

        // start : isi kolom
        $no = 1;
        $totalBiaya = 0;
        $totalRealisasi = 0;
        foreach ($res as $value) {
            $col = 1;
            $abjad = 'A';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_aktivitas);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $tonase = MaterialTrans::select('jumlah')->where('id_aktivitas_harian', $value->id)->get();
            $jumlahTonase = 0;
            foreach ($tonase as $key) {
                $jumlahTonase += $key->jumlah;
            }
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $jumlahTonase);
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->anggaran_tkbm);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($jumlahTonase * $value->anggaran_tkbm));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            $biaya = $jumlahTonase * $value->anggaran_tkbm;
            $totalBiaya += $biaya;
            $totalRealisasi += $jumlahTonase;

            $row++;
            $no++;
        }

        $col = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':C' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

        $abjad = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Biaya');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_ontop);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col = 4;
        $abjad = 'D';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalRealisasi, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '-');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
        
        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalBiaya);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom)->getNumberFormat()->setFormatCode('#,##0');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Biaya TKBM');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanBiayaPallet()
    {
        $data['title'] = 'Laporan Biaya Pallet';
        $data['aktivitas'] = Aktivitas::get();
        $data['jenisAlatBerat'] = KategoriAlatBerat::get();
        $gudang = Gudang::internal();

        $localGudang = $this->getCheckerGudang(auth()->user()->role_id);

        if ($localGudang) {
            $gudang = $gudang->where('id', $localGudang->id);
        }
        $data['gudang'] = $gudang->get();
        return view('report.biaya-pallet.grid', $data);
    }

    public function biayaPallet()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'tgl_awal'  => 'required',
                'tgl_akhir' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi!',
            ],
            [
                'tgl_awal'  => 'Tanggal Awal',
                'tgl_akhir' => 'Tanggal Akhir',
            ]
        );

        if ($validator->fails()) {
            return redirect('report/laporan-biaya-tkbm')
                ->withErrors($validator)
                ->withInput();
        }
        $gudang             = request()->input('gudang');
        $aktivitas          = request()->input('aktivitas');
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));

        $res = DB::table('aktivitas_harian')
            ->select(
            'aktivitas_harian.*',
            'aktivitas.anggaran_pallet',
            'gudang.nama as nama_gudang',
            'aktivitas.nama as nama_aktivitas'
            )
            ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->leftJoin('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
            ->whereBetween('aktivitas_harian.updated_at', [$tgl_awal, $tgl_akhir])
            ->whereNotNull('biaya_pallet')
            ->latest('aktivitas_harian.updated_at')
            ;

        $nama_file = date("YmdHis") . '_biaya_pallet.xlsx';

        $resGudang = Gudang::get();
        if ($gudang) {
            $res = $res->where(function ($query) use ($gudang) {
                $query->where('id_gudang', $gudang[0]);
                foreach ($gudang as $key => $value) {
                    $query->orWhere('id_gudang', $value);
                }
            });
            $resGudang      = Gudang::whereIn('id', $gudang)->get();
        }

        $resAktivitas = Aktivitas::get();
        if ($aktivitas) {
            $res = $res->where(function ($query) use ($aktivitas) {
                $query->where('id_aktivitas', $aktivitas[0]);
                foreach ($aktivitas as $key => $value) {
                    $query->orWhere('id_aktivitas', $value);
                }
            });
            $resAktivitas   = Aktivitas::whereIn('id', $aktivitas)->get();
        }

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }
        $res = $res->get();

        $this->generateExcelBiayaPallet($res, $nama_file, $resGudang, $resAktivitas, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelBiayaPallet($res, $nama_file, $resGudang, $resAktivitas, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;

        //start: style
        $style_title = array(
            'font' => array(
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
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
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
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
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

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REPORT BIAYA PALLET');
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PERIODE : ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'GUDANG');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'AKTIVITAS');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI TONASE MUAT PALLET');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'BIAYA Rp/Ton (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'REALISASI BIAYA PALLET (Rupiah)');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $row = 7;
        // end : judul kolom

        // start : isi kolom
        $no = 1;
        $totalBiaya = 0;
        $totalRealisasi = 0;
        foreach ($res as $value) {
            $col = 1;
            $abjad = 'A';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_gudang);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_aktivitas);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $tonase = MaterialTrans::select('jumlah')->whereNotNull('status_pallet')->where('id_aktivitas_harian', $value->id)->get();
            $jumlahTonase = 0;
            foreach ($tonase as $key) {
                $jumlahTonase += $key->jumlah;
            }
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $jumlahTonase);
            $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($value->anggaran_pallet));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($jumlahTonase * $value->anggaran_pallet));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom)->getNumberFormat()->setFormatCode('#,##0');
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);
            $biaya = $jumlahTonase * $value->anggaran_pallet;
            $totalBiaya += $biaya;
            $totalRealisasi += $jumlahTonase;

            $row++;
            $no++;
        }

        $col = 1;
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':C' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '');

        
        $abjad = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Biaya');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_ontop);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col = 4;
        $abjad = 'D';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, round($totalRealisasi, 3));
        $objSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '-');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        $col++;
        $abjad++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalBiaya);
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_isi_kolom)->getNumberFormat()->setFormatCode('#,##0');
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Biaya Pallet');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }

    public function laporanKeluhanOperator()
    {
        $data['title'] = 'Laporan Keluhan Operator';
        $data['keluhan'] = Keluhan::get();
        return view('report.keluhan-operator.grid', $data);
    }

    public function keluhanOperator()
    {
        $keluhan           = request()->input('keluhan'); //multi
        $tgl_awal           = date('Y-m-d', strtotime(request()->input('tgl_awal')));
        $tgl_akhir          = date('Y-m-d', strtotime(request()->input('tgl_akhir') . '+1 day'));
        $res = KeluhanOperator::select(
            'tenaga_kerja_non_organik.nama as nama_operator',
            'tenaga_kerja_non_organik.nik',
            'keluhan.nama as nama_keluhan',
            'keterangan',
            'keluhan_operator.created_at as tanggal',
            'keluhan_operator.created_by'
        )
        ->join('tenaga_kerja_non_organik', 'tenaga_kerja_non_organik.id', '=', 'keluhan_operator.id_operator')
        ->join('keluhan', 'keluhan.id', '=', 'keluhan_operator.id_keluhan')
            ;
        
        if ($keluhan) {
            $res = $res->where('keluhan_operator.id_keluhan', $keluhan[0]);
            foreach ($keluhan as $key => $value) {
                $res = $res->orWhere('keluhan_operator.id_keluhan', $value);
            }
        }

        $res = $res->orderBy('keluhan_operator.created_at')->get();

        $preview = false;
        if (request()->preview == true) {
            $preview = true;
        }

        $nama_file = date("YmdHis") . '_keluhan_operator.xlsx';
        $this->generateExcelKeluhanOperator($res, $nama_file, $tgl_awal, $tgl_akhir, $preview);
    }

    public function generateExcelKeluhanOperator($res, $nama_file, $tgl_awal, $tgl_akhir, $preview)
    {
        $objSpreadsheet = new Spreadsheet();

        $sheetIndex = 0;
        //start: style
        $style_title = array(
            'font' => array(
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
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                )
            ),
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
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '98d6ea')
            ),
            'font' => array(
                'bold' => true
            ),
        );
        $style_note = array(
            'font' => array(
                'bold' => true
            )
        );
        //end: style

        $objSpreadsheet->createSheet($sheetIndex);
        $objSpreadsheet->setActiveSheetIndex($sheetIndex);

        $objSpreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objSpreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        // start : title
        $col = 1;
        $row = 1;
        $objSpreadsheet->getActiveSheet()->setShowGridlines(false);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'LAPORAN KELUHAN OPERATOR');
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);

        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'PERIODE : ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_title);
        $objSpreadsheet->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);

        $col = 1;
        $row++;

        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_acara);
        $objSpreadsheet->getActiveSheet()->getStyle('A' . $row)->applyFromArray($style_note);

        // end : title
        // start : judul kolom
        $col = 1;
        $row = 6;
        $abjadOri = 'A';
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA GUDANG');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NAMA OPERATOR');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'NO. BADGE');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'JENIS KELUHAN');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $abjadOri++;
        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'KETERANGAN');
        $objSpreadsheet->getActiveSheet()->getStyle($abjadOri . $row)->applyFromArray($style_judul_kolom);

        $row = 7;
        // end : judul kolom

        // start : isi kolom
        $no = 1;
        foreach ($res as $value) {
            $col = 1;
            $abjad = 'A';
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_no);

            $user = Users::find($value->created_by);
            $karu = Karu::find($user->id_karu);
            $gudang = Gudang::find($karu->id_gudang);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $gudang->nama);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_operator);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nik);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y', strtotime($value->tanggal)));
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->nama_keluhan);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $col++;
            $abjad++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->keterangan);
            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row)->applyFromArray($style_kolom);

            $row++;
            $no++;
        }

        //Sheet Title
        $objSpreadsheet->getActiveSheet()->setTitle('Laporan Keluhan Operator');
        // end : isi kolom
        // end : sheet

        #### END : SHEET SESI ####
        if ($preview == true) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($objSpreadsheet);
            echo $writer->generateHTMLHeader();
            echo $writer->generateStyles(true);
            echo $writer->generateSheetData();
            echo $writer->generateHTMLFooter();
        } else {
            $writer = new Xlsx($objSpreadsheet);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nama_file . '"');
            $writer->save("php://output");
        }
    }
}
