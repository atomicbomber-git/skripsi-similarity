<?php

use App\Http\Controllers\BankSkripsiMahasiswaController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\SkripsiDeleteController;
use App\Http\Controllers\SkripsiFileUploadController;
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

Route::get('bank-skripsi-mahasiswa', BankSkripsiMahasiswaController::class)
    ->name('bank-skripsi-mahasiswa');

Route::get('/mahasiswa/{mahasiswa}/dashboard', MahasiswaDashboardController::class)
    ->name("mahasiswa.dashboard");

Route::get('/mahasiswa/{mahasiswa}/download-skripsi', SkripsiFileDownloadController::class)
    ->name("mahasiswa.download-skripsi");

Route::post('/mahasiswa/{mahasiswa}/upload-skripsi', SkripsiFileUploadController::class)
    ->name("mahasiswa.upload-skripsi");

Route::delete('/mahasiswa/{mahasiswa}/delete-skripsi', SkripsiDeleteController::class)
    ->name("mahasiswa.delete-skripsi");

Route::view("/ViewerJS/{all?}", "ViewerJS.index")->name("ViewerJS");
