<?php

namespace App\Jobs;

use App\Http\Models\MaterialTrans;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $option;
    private $parameter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($option, $parameter)
    {
        $this->option = $option;
        $this->parameter = $parameter;
    }

    private function generateExcelMaterial($res, $nama_file, $gudang, $tgl_awal, $tgl_akhir)
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'TANGGAL ' . strtoupper(helpDate($tgl_awal, 'li')) . ' - ' . strtoupper(helpDate($tgl_akhir, 'li')));
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
        $objSpreadsheet->getActiveSheet()->mergeCells($abjadPemasukan . ($row - 1) . ':' . $abjadPengeluaran . ($row - 1));

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
                ->sum('jumlah');
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

            foreach ($gudang as $item) {
                $materialTrans = MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'material_trans.id_adjustment')
                    ->whereHas('areaStok.area', function ($query) use ($item) {
                        $query->where('id_gudang', $item->id);
                    })
                    ->where('tipe', 2)
                    ->where('id_material', $value->id_material)
                    ->where(function ($query) use ($tgl_awal, $tgl_akhir) {
                        $query->whereBetween('aktivitas_harian.created_at', [$tgl_awal, $tgl_akhir]);
                        $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
                    })
                    ->where('status_produk', 1)
                    ->sum('jumlah');
                $stokAkhir += $materialTrans;
                $col++;
                $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $materialTrans);
            }

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
                        $query->orWhereBetween('material_adjustment.created_at', [$tgl_awal, $tgl_akhir]);
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

            $siapJual = $stokAkhir - $rusak;

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
        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save("php://output");
    }

    private function generateExcelTransaksiMaterial($res, $nama_file, $tgl_awal, $tgl_akhir)
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Laporan Transaksi Material');
        $objSpreadsheet->getActiveSheet()->getStyle("C" . $row)->applyFromArray($style_title);

        $row++;
        $objSpreadsheet->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Peridode: ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjadOri . $row)->applyFromArray($style_judul_kolom);
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

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ":" . $abjad . $row)->applyFromArray($style_kolom);

            $objSpreadsheet->getActiveSheet()->getStyle($abjad . $row . ':' . $abjad . $row)->applyFromArray($style_ontop);

            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->material->nama);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->tipe == 1 ? 'Mengurangi' : 'Menambah');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value->jumlah);

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, date('d-m-Y', strtotime($value->created_at)));

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudang)) ? $value->aktivitasHarian->gudang->nama : '');

            $col++;
            $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, (!empty($value->aktivitasHarian->gudangTujuan)) ? $value->aktivitasHarian->gudangTujuan->nama : '');

            $tempRes =  DB::table('material_trans')
                ->leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->where('id_material', $value->material->id)
                ->where('aktivitas_harian.created_at', '<', $value->created_at);

            $penambahan = $tempRes->where('tipe', 2)->sum('jumlah');
            $pengurangan = $tempRes->where('tipe', 1)->sum('jumlah');

            $jumlahStok = $penambahan + $pengurangan;

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

        $totalNormal = $totalStok - $totalRusak;

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

    private function generateExcelStok($res, $nama_file, $produk, $area, $tgl_awal, $tgl_akhir)
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
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Periode Tanggal ' . date('d/m/Y', strtotime($tgl_awal)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir . '-1 day')));
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
        $j = 0;
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
                $singleton = DB::table('material_trans')->where('id_material', $key->id)
                    ->where('status_produk', 1) //harus + 2 step agar cocok dengan status pada databse
                    ->where('material_trans.id_area', $value->id)
                    // ->join('area', function ($join) use ($value) {
                    //     $join->on('area.id', '=', 'material_trans.id_area');
                    // })
                    ->leftJoin('aktivitas_harian', function ($join) use ($value) {
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')->where('draft', 0);
                    })
                    ->whereBetween('material_trans.created_at', [$tgl_awal, $tgl_akhir]);

                $masuk      = $singleton
                    ->where('material_trans.tipe', 2)
                    ->sum('jumlah');

                $keluar     = $singleton
                    ->where('material_trans.tipe', 1)
                    ->sum('jumlah');

                // dd($masuk);

                $jumlah  = $masuk - $keluar;
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

        $col = 2;
        $row++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Jumlah'); //jumlah

        $col++;
        $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total_kapasitas); //jumlah
        $total_semua = 0;
        $abjadKedua = 'C';
        for ($i = 0; $i < count($total); $i++) {
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
        $writer->save(storage_path() . '/app/public/excel/' . $nama_file);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(storage_path() . '/app/public/excel/' . $nama_file);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Pragma: no-cache');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama_file . '"');
        $writer->save('php://output');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($event);
        // if ($option == 'transaksi_material') {
        //     $this->generateExcelTransaksiMaterial($parameter['res'], $parameter['nama_file'], $parameter['tgl_awal'], $parameter['tgl_akhir']);
        // }

        // if ($option == 'material') {
        //     $this->generateExcelMaterial($parameter['res'], $parameter['nama_file'], $parameter['resGudang'], $parameter['tgl_awal'], $parameter['tgl_akhir']);
        // }

        if ($this->option == 'stok') {
            $this->generateExcelStok($this->parameter['res'], $this->parameter['nama_file'], $this->parameter['resProduk'], $this->parameter['resArea'], $this->parameter['tgl_awal'], $this->parameter['tgl_akhir']);
            return response()->download(storage_path() . '/app/public/excel/' . $this->parameter['nama_file']);
        }
    }
}
