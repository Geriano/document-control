<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::resource('approver', App\Http\Controllers\ApproverController::class);
    Route::resource('approval', App\Http\Controllers\ApprovalController::class);
    Route::resource('document', App\Http\Controllers\DocumentController::class);
    Route::get('/document/{document}/revisions', [App\Http\Controllers\DocumentController::class, 'revisions'])->name('document.revisions');
    Route::resource('revision', App\Http\Controllers\RevisionController::class);
    Route::resource('procedur', App\Http\Controllers\ProcedurController::class);
    Route::patch('/procedur/{procedur}/left', [App\Http\Controllers\ProcedurController::class, 'left'])->name('procedur.left');
    Route::patch('/procedur/{procedur}/right', [App\Http\Controllers\ProcedurController::class, 'right'])->name('procedur.right');
    Route::patch('/procedur/{procedur}/up', [App\Http\Controllers\ProcedurController::class, 'up'])->name('procedur.up');
    Route::patch('/procedur/{procedur}/down', [App\Http\Controllers\ProcedurController::class, 'down'])->name('procedur.down');
    Route::patch('/procedur-drill', [App\Http\Controllers\ProcedurController::class, 'drill'])->name('procedur.drill');
    Route::resource('content', App\Http\Controllers\ContentController::class);
});

Route::middleware(['auth:sanctum', 'role:superuser'])->prefix('superuser')->name('superuser.')->group(fn () => require __DIR__ . '/web/superuser.php');
Route::get('/test', [App\Http\Controllers\Test::class, 'index']);