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
    Route::resource('procedure', App\Http\Controllers\ProcedureController::class);
    Route::patch('/procedure/{procedure}/left', [App\Http\Controllers\ProcedureController::class, 'left'])->name('procedure.left');
    Route::patch('/procedure/{procedure}/right', [App\Http\Controllers\ProcedureController::class, 'right'])->name('procedure.right');
    Route::patch('/procedure/{procedure}/up', [App\Http\Controllers\ProcedureController::class, 'up'])->name('procedure.up');
    Route::patch('/procedure/{procedure}/down', [App\Http\Controllers\ProcedureController::class, 'down'])->name('procedure.down');
    Route::patch('/procedure-drill', [App\Http\Controllers\ProcedureController::class, 'drill'])->name('procedure.drill');
    Route::resource('content', App\Http\Controllers\ContentController::class);
});

Route::middleware(['auth:sanctum', 'role:superuser'])->prefix('superuser')->name('superuser.')->group(fn () => require __DIR__ . '/web/superuser.php');
Route::get('/test', [App\Http\Controllers\Test::class, 'index']);