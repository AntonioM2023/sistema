<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('auth.login');
});
//Route::get('/empleado', function () {
 //   return view('Empleado.index');
//});

//Route::get('/empleado/create', [EmpleadoController::class,'create']);

Route::resource('empleado', EmpleadoController::class)->middleware('auth');

Auth::routes(['register'=>false,'reset'=>false]);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Lo siguiente es para que cuando se acceda a "/home" nos lleve directamente al CRUD.
Route::get('/home', [EmpleadoController::class, 'index'])->name('home');

//Lo siguiente es para cuando el usuario se loggee, que automáticamente vaya al controlador "EmpleadoController", concretamente al método "index"
Route::group(['middleware'=>'auth'], function(){
    Route::get('/', [EmpleadoController::class, 'index'])->name('home');
});
