<?php

use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Material;
use Illuminate\Database\Seeder;

class AreaStokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $material = Material::produk()->get();
        $area = Area::all();

        // if ($hapus) {
        //     AreaStok::truncate();
        // }

        foreach ($area as $keyArea) {
            foreach ($material as $keyMaterial) {
                $areaStok = new AreaStok;
                $areaStok->fill([
                    'id_area'       => $keyArea->id,
                    'id_material'   => $keyMaterial->id,
                    'tanggal'       => '2019-12-20',
                    'jumlah'        => 100
                ])->save();

                $areaStok = new AreaStok;
                $areaStok->fill([
                    'id_area'       => $keyArea->id,
                    'id_material'   => $keyMaterial->id,
                    'tanggal'       => '2019-12-25',
                    'jumlah'        => 100
                ])->save();

                // $areaStok->materialTrans()->saveMany([
                //     new MaterialTrans()
                // ]);
            }
        }
    }
}
