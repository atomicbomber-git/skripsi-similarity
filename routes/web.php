<?php

use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\UploadFileUploadController;
use App\Http\Controllers\SkripsiFileDownloadController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();


Route::get('/', function () {
    return redirect()->route("login");
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('/mahasiswa', MahasiswaController::class);

Route::get('/mahasiswa/{mahasiswa}/dashboard', MahasiswaDashboardController::class)
    ->name("mahasiswa.dashboard");

Route::get('/mahasiswa/{mahasiswa}/download-skripsi', SkripsiFileDownloadController::class)
    ->name("mahasiswa.download-skripsi");

Route::post('/mahasiswa/{mahasiswa}/upload-skripsi', UploadFileUploadController::class)
    ->name("mahasiswa.upload-skripsi");
