<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoursesController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [CoursesController::class ,'courses_view'])->name('home');
Route::get('/courses', [CoursesController::class ,'courses_view_list'])->name('courses-view');
Route::get('/courses/add', [CoursesController::class ,'courseAdd'])->name('courses-add');
Route::post('/course/create/', [CoursesController::class ,'courseCreate'])->name('storeCourse');
Route::delete('course/delete/{id}', [CoursesController::class,'destroyCourse'])->name('destroy');