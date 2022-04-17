<?php

use App\Http\Controllers\Web;
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

Route::get('/', [Web\MainController::class, 'home'])
	->name('home');

Route::delete('/reset', [Web\MainController::class, 'reset'])
	->name('reset');

Route::post('/generate', [Web\MainController::class, 'generate'])
	->name('generate');

Route::post('/play_next', [Web\MainController::class, 'playNext'])
	->name('play_next');

Route::post('/play_all', [Web\MainController::class, 'playAll'])
	->name('play_all');