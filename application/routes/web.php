<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Auth\LoginController@index');
Route::get('/login', 'Auth\LoginController@index')->name('login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/authenticate', 'Auth\LoginController@authenticate')->name('authenticate');



Route::get('/master-grup', function () {
    return view('master.master-grup.grid');
});

Route::group(['middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/dashboard', function () {
        return View::make('dashboard.grid');
    });
    Route::get('/', 'DashboardController@index')->name('/');
    
    Route::group(['prefix' => 'master-aktivitas'], function () {
        Route::get('/', 'AktivitasController@index');
        Route::get('/tambah', 'AktivitasController@create');
        Route::get('/edit/{id}', 'AktivitasController@edit')->where('id', '[0-9]+');
        Route::put('/', 'AktivitasController@store');
        Route::patch('/{aktivitas}', 'AktivitasController@store');
        Route::post('/', 'AktivitasController@json');
        Route::get('/{id}', 'AktivitasController@show')->where('id', '[0-9]+');
        Route::get('/get-upload-foto/{id}', 'AktivitasController@getFotoOfAktivitas')->where('id', '[0-9]+');
        Route::get('/get-alat-berat/{id}', 'AktivitasController@getAlatBeratOfAktivitas')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'master-karu'], function () {
        Route::get('/', 'KaruController@index');
        Route::put('/', 'KaruController@store');
        Route::patch('/{karu}', 'KaruController@store')->where('karu', '[0-9]+');
        Route::post('/', 'KaruController@json');
        Route::get('/{id}', 'KaruController@show')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'master-material'], function () {
        Route::get('/', 'MaterialController@index');
        Route::put('/', 'MaterialController@store');
        Route::patch('/{material}', 'MaterialController@store')->where('material', '[0-9]+');
        Route::post('/', 'MaterialController@json');
        Route::get('/{id}', 'MaterialController@show')->where('id', '[0-9]+');
        Route::get('/sap/{id?}', 'MaterialController@getSap')->where('id', '[0-9]+');
        Route::get('/get-material-sap/{id}', 'MaterialController@getMaterialSap')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'master-tenaga-kerja-nonorganik'], function () {
        Route::get('/', 'TenagaKerjaNonOrganikController@index');
        Route::put('/', 'TenagaKerjaNonOrganikController@store');
        Route::post('/', 'TenagaKerjaNonOrganikController@json');
        Route::get('/{id}', 'TenagaKerjaNonOrganikController@show')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'master-kerusakan-alat'], function () {
        Route::get('/', 'AlatBeratKerusakanController@index');
        Route::put('/', 'AlatBeratKerusakanController@store');
        Route::post('/', 'AlatBeratKerusakanController@json');
        Route::get('/{id}', 'AlatBeratKerusakanController@show')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'master-kategori-alat-berat'], function () {
        Route::get('/', 'KategoriAlatBeratController@index');
        Route::put('/', 'KategoriAlatBeratController@store');
        Route::post('/', 'KategoriAlatBeratController@json');
        Route::get('/{id}', 'KategoriAlatBeratController@show')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'list-alat-berat'], function () {
        Route::get('/{id}', 'AlatBeratController@index')->where('id', '[0-9]+');
        Route::put('/{id}', 'AlatBeratController@store')->where('id', '[0-9]+');
        Route::post('/{id}', 'AlatBeratController@json')->where('id', '[0-9]+');
        Route::get('/{kategoriAlatBerat}/{alatBerat}', 'AlatBeratController@show')->where(['kategoriAlatBerat' => '[0-9]+', 'alatBerat' => '[0-9]+']);
    });

    Route::group(['prefix' => 'master-jenis-foto'], function () {
        Route::get('/', 'JenisFotoController@index');
        Route::put('/', 'JenisFotoController@store');
        Route::patch('/{jenisFoto}', 'JenisFotoController@store')->where('jenisFoto', '[0-9]+');
        Route::post('/', 'JenisFotoController@json');
        Route::get('/{jenisFoto}', 'JenisFotoController@show')->where('jenisFoto', '[0-9]+');
    });

    Route::group(['prefix' => 'master-user'], function () {
        Route::get('/', 'UsersController@index');
        Route::get('/load-pegawai/{id_kategori}', 'UsersController@loadPegawai');
        Route::put('/', 'UsersController@store');
        Route::post('/', 'UsersController@json');
        Route::get('/{id}', 'UsersController@show')->where('id', '[0-9]+');
        Route::patch('/{id}', 'UsersController@changePassword')->where('id', '[0-9]+');
        Route::delete('/{id}', 'UsersController@destroy')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'gudang'], function () {
        Route::get('/', 'GudangController@index');
        Route::get('/load-pallet', 'GudangController@loadPallet');
        Route::get('/get-produk', 'GudangController@getProduk');
        Route::get('/get-pallet', 'GudangController@getPallet');
        Route::put('/', 'GudangController@store');
        Route::post('/', 'GudangController@json');
        Route::get('/load-material/{id_gudang}', 'GudangController@loadMaterial')->where('id_gudang', '[0-9]+');
        Route::get('/{id}', 'GudangController@show')->where('id', '[0-9]+');
        Route::delete('/{id}', 'GudangController@destroy');
        
        Route::group(['prefix' => 'stock-adjustment'], function () {
            Route::get('/{id_gudang}', 'MaterialAdjustmentController@index')->where('id_gudang', '[0-9]+');
            Route::get('/{id_gudang}/{id}', 'MaterialAdjustmentController@show')->where('id_gudang', '[0-9]+');
            Route::delete('/{id_gudang}/{materialAdjustment}', 'MaterialAdjustmentController@destroy')->where(['id_gudang' => '[0-9]+', 'materialAdjustment' => '[0-9]+']);
            Route::post('/{id_gudang}', 'MaterialAdjustmentController@json')->where('id_gudang', '[0-9]+');
            Route::put('/{id_gudang}', 'MaterialAdjustmentController@store')->where('id_gudang', '[0-9]+');
            Route::post('/upload/{id_gudang}', 'MaterialAdjustmentController@uploadFile')->where('id_gudang', '[0-9]+');
        });
    });

    Route::group(['prefix' => 'list-area'], function () {
        Route::get('/{id}', 'AreaController@index');
        Route::put('/{id}', 'AreaController@store')->where('id', '[0-9]+');
        Route::post('/{id}', 'AreaController@json')->where('id', '[0-9]+');
        Route::get('/{id}/{id_area}', 'AreaController@show')->where(['id' => '[0-9]+', 'id_area' => '[0-9]+']);
        Route::delete('/{id}/{id_area}', 'AreaController@destroy')->where(['id' => '[0-9]+', 'id_area' => '[0-9]+']);
    });

    Route::group(['prefix' => '/list-pallet'], function () {
        Route::get('/get-material', 'PalletController@getMaterial');
        Route::get('/{id_gudang}', 'PalletController@index');
        Route::get('/{id_gudang}/{id}', 'PalletController@show');
        Route::post('/{id_gudang}', 'PalletController@json');
        Route::put('/{id_gudang}', 'PalletController@store');
    });

    Route::group(['prefix' => 'rencana-harian'], function () {
        Route::get('/', 'RencanaHarianController@index');
        Route::get('/get-area/{id_gudang?}', 'RencanaHarianController@getArea')->where('id_gudang', '[0-9]+');
        Route::get('/tambah', 'RencanaHarianController@create');
        Route::get('/ubah/{rencana_harian}', 'RencanaHarianController@edit')->where('rencana_harian', '[0-9]+');
        Route::get('/get-alat-berat', 'RencanaHarianController@getAlatBerat');
        Route::get('/get-gudang', 'RencanaHarianController@getGudang');
        Route::put('/', 'RencanaHarianController@store');
        Route::patch('/{rencana_harian}', 'RencanaHarianController@update')->where('rencana_harian', '[0-9]+');
        Route::post('/', 'RencanaHarianController@json');
        Route::get('/{rencana_harian}', 'RencanaHarianController@show')->where('rencana_harian', '[0-9]+');
        Route::get('/get-rencana-tkbm/{id_job_desk}/{id_rencana}/', 'RencanaHarianController@getRencanaTkbm')->where(['id_job_desk' => '[0-9]+', 'id_rencana' => '[0-9]+']);
        Route::get('/get-tkbm/{id}', 'RencanaHarianController@getTkbm')->where('id', '[0-9]+');
        Route::get('/get-rencana-alat-berat/{id_rencana}/', 'RencanaHarianController@getRencanaAlatBerat')->where('id_rencana', '[0-9]+');
        Route::get('/get-rencana-tkbm-area/{id_rencana}/{id_tkbm?}', 'RencanaHarianController@getRencanaAreaTkbm')->where(['id_rencana' => '[0-9]+', 'id_tkbm' => '[0-9]+']);
        Route::get('/get-material/{kategori}', 'RencanaHarianController@getMaterial')->where('kategori', '[0-9]+');
        Route::delete('/{id}', 'RencanaHarianController@destroy');
       
        Route::group(['prefix' => 'realisasi'], function () {
            Route::get('/{rencanaHarian}', 'RencanaHarianController@realisasi')->where('rencanaHarian', '[0-9]+');
            Route::get('/get-housekeeper/{id_rencana}', 'RencanaHarianController@getHouseKeeper')->where('id_rencana', '[0-9]+');
            Route::put('/{rencanaHarian}/{realisasi?}', 'RencanaHarianController@storeRealisasi')->where(['rencanaHarian' => '[0-9]+', 'realisasi' => '[0-9]+']);
        });
    });

    Route::get('/layout', function () {
        return view('menu-layout.grid');
    });

    Route::group(['prefix' => 'penerimaan-gp'], function () {
        Route::get('/', 'PenerimaanGpController@index');
        Route::get('/{aktivitasHarian}', 'PenerimaanGpController@show')->where('aktivitasHarian', '[0-9]+');
        Route::post('/', 'PenerimaanGpController@json');
        Route::patch('/{aktivitasHarian}', 'PenerimaanGpController@store')->where('aktivitasHarian', '[0-9]+');
    });

    // Route::get('/penerimaan-gp', function () {
    //     return view('penerimaan-gp.grid');
    // });
    // Route::get('/penerimaan-gp/detail', function () {
    //     return view('penerimaan-gp.detail');
    // });

    Route::group(['prefix' => 'log-aktivitas'], function () {
        Route::get('/', 'LogAktivitasController@index');
        Route::post('/', 'LogAktivitasController@json');
        Route::get('/{aktivitasHarian}', 'LogAktivitasController@show')->where('aktivitasHarian', '[0-9]+');
    });
});

Route::get('watch/{nama}/', 'WatchController@default');

// Route::get('/master-material', function () {
//     return view('master.master-material.grid');
// });

// Route::get('/gudang', function () {
//     return view('gudang.grid');
// });
Route::get('/sub-gudang', function () {
    return view('sub-gudang.grid');
});
// Route::get('/master-alat-berat/list-alat-berat', function () {
//     return view('list-alat-berat.grid');
// });
// Route::get('/stok-adjustment', function () {
//     return view('stok-adjusment.grid');
// });
// Route::get('/gudang/list-alat-berat', function () {
//     return view('list-alat-berat-gudang.grid');
// });

Route::get('/list-tenaga-kerja-nonorganik', function () {
    return view('list-tenaga-kerja-nonorganik.grid');
});

Route::get('/list-area', function () {
    return view('list-area.grid');
});
Route::get('/anggaran-alat-berat', function () {
    return view('anggaran-alat-berat.grid');
});
Route::get('/anggaran-sdm', function () {
    return view('anggaran-sdm.grid');
});


// Route::get('/rencana-harian', function () {
//     return view('rencana-harian.grid');
// });
Route::get('/add-rencana-harian', function () {
    return view('rencana-harian.add');
});
// Route::get('/realisasi', function () {
//     return view('rencana-harian.realisasi');
// });
// Route::get('/log-aktivitas', function () {
//     return view('aktivitas.grid');
// });
// Route::get('/log-aktivitas/detail', function () {
//     return view('aktivitas.detail');
// });
// Route::get('/log-aktivitas/tambah', function () {
//     return view('log-aktivitas.add');
// });



// Route::get('/log-aktivitas', function () {
//     return view('log-aktivitas.grid');
// });
// Route::get('/log-aktivitas/detail', function () {
//     return view('log-aktivitas.detail');
// });







Route::get('/master-pemetaan-sloc', function () {
    return view('master.master-pemetaan-sloc.grid');
});

Route::get('/main', function () {
    return view('layout.main');
});


Route::get('/laporan-material', function () {
    return view('report.material.grid');
});
Route::get('/laporan-stok', function () {
    return view('report.stok.grid');
});
Route::get('/laporan-absen-karyawan', function () {
    return view('report.karyawan.grid');
});
Route::get('/laporan-mutasi-pallet', function () {
    return view('report.mutasi-pallet.grid');
});
Route::get('/laporan-mutasi-stok', function () {
    return view('report.mutasi-stok.grid');
});
Route::get('/laporan-produk', function () {
    return view('report.produk.grid');
});
Route::get('/laporan-realisasi', function () {
    return view('report.realisasi.grid');
});
Route::get('/laporan-keluhan-alat-berat', function () {
    return view('report.keluhan-alat-berat.grid');
});
Route::get('/laporan-keluhan-gp', function () {
    return view('report.keluhan-gp.grid');
});