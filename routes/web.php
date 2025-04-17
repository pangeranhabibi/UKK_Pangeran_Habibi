<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PembelianController;  // Pastikan controller ini ada

Route::middleware('IsGuest')->group(function() {
    Route::get('/', function () {
        return view('login');
    })->name('login');
    
    Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');
});

Route::middleware(['IsLogin'])->group(function(){
    Route::get('/home', [PembelianController::class, 'showDashboard'])->name('home.page');  // Tambahkan route hweome.page
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
});

Route::middleware(['IsLogin', 'IsAdmin'])->group(function(){
    Route::prefix('/Admin')->name('user.')->group(function(){
        Route::get('/user', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/export-user', [UserController::class, 'export'])->name('export');
    });
});

Route::prefix('/ukk-kasir')->name('produk.')->group(function(){
    Route::get('/produk', [ProdukController::class, 'index'])->name('index');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('create');
    Route::post('/produk/store', [ProdukController::class, 'store'])->name('store');
    Route::get('/produk/edit/{id}', [ProdukController::class, 'edit'])->name('edit');
    Route::patch('/produk/update/{id}', [ProdukController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [ProdukController::class, 'destroy'])->name('destroy');
    Route::put('/update-stok/{id}', [ProdukController::class, 'updateStock'])->name('stok');
    Route::get('/produk/export', [ProdukController::class, 'export'])->name('exportExcel');


});

Route::prefix('/ukk-kasir')->name('pembelian.')->group(function(){
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('index');
    Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('create');
    Route::get('/pembelian/sale', [PembelianController::class, 'sale'])->name('sale');
    Route::post('/pembelian/sale/store', [PembelianController::class, 'store'])->name('store');
    Route::get('/pembelian/sale/detail/{id}', [PembelianController::class, 'show'])->name('show');
    Route::get('/pembelian/sale/member/{id}', [PembelianController::class, 'showMember'])->name('member');
    Route::post('/pembelian/sale/member/store', [PembelianController::class, 'memberStore'])->name('memberStore');
    Route::post('pembelian/export-pdf/{id}', [PembelianController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export-transactions', [PembelianController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/pembelian/detail', [PembelianController::class, 'detail'])->name('detail');
});

