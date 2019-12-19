<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use Illuminate\Http\Request;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\MaterialAdjustment;
use App\Http\Models\RealisasiHousekeeper;

class WatchController extends Controller
{
    public function default($nama, Request $request)
    {
        $access_token       = helpEmpty($request->get("token"), 'null');
        $id_file            = helpEmpty($request->get("un"), 'null');
        $id_parent          = helpEmpty($request->get("prt"), 'null');
        $category           = helpEmpty($request->get("ctg"), 'null');
        $source             = helpEmpty($request->get("src"), 'null');

        $image         = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'];

        $file = myBasePath();

        $cek_id = '';

        if ($category == 'material') {
            $cek_id = MaterialAdjustment::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        } else if ($category == 'aktivitas_foto') {
            $cek_id = AktivitasFoto::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        } else if ($category == 'history') {
            $cek_id = LaporanKerusakan::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        } else if ($category == 'aktivitas_harian') {
            $cek_id = AktivitasHarian::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        } else if ($category == 'kelayakan') {
            $cek_id = AktivitasHarian::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        } else if ($category == 'realisasi_housekeeper') {
            $cek_id = RealisasiHousekeeper::find($id_file);

            if (!empty($source) && !empty($category) && !empty($cek_id)) {
                $file = storage_path('app/public/' . $category . '/' . $id_file . '/' . $source);
            }
        }

        $file = protectPath($file);

        if (file_exists($file) && !is_dir($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $ext = strtolower($ext);

            if (in_array(strtolower($ext), $image)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit;
            } else {
                header('Content-Type:' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file));
                header('Content-Length: ' . filesize($file));
                readfile($file);
            }
        } else {
            $response = helpResponse(404);
            return response()->json($response, 404);
        }
    }
}
