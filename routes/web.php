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

    Route::prefix('/approval')->name('approval.')->controller(App\Http\Controllers\ApprovalController::class)->group(function () {
        Route::get('/documents', 'documents')->name('document.index');
        Route::post('/documents', 'paginateDocuments')->name('document.paginate');
        Route::get('/revisions', 'revisions')->name('revision.index');
        Route::post('/revisions', 'paginateRevisions')->name('revision.paginate');
    });
    Route::resource('approval', App\Http\Controllers\ApprovalController::class);
    
    Route::resource('approver', App\Http\Controllers\ApproverController::class);
    Route::prefix('/approver')->name('approver.')->controller(App\Http\Controllers\ApproverController::class)->group(function () {
        Route::patch('/{approver}/up', 'up')->name('up');
        Route::patch('/{approver}/down', 'down')->name('down');
    });

    Route::resource('document', App\Http\Controllers\DocumentController::class);
    Route::prefix('/document')->name('document.')->controller(App\Http\Controllers\DocumentController::class)->group(function () {
        Route::get('/{document}/revisions', 'revisions')->name('revisions');
        Route::get('/{document}/approvers', 'approvers')->name('approvers');
        Route::delete('/{approver}/detach', 'detachApprover')->name('approver.detach');
        Route::post('/{document}/approvers/{user}', 'addApproverFor')->name('approver.add');
        Route::patch('/{approver}/approvers/{user}', 'updateApprover')->name('approver.update');
        Route::get('/{document}/approvals', 'approvals')->name('approvals');
        Route::post('/{document}/request', 'request')->name('approval.request');
        Route::patch('/{document}/approve', 'approve')->name('approve');
        Route::patch('/{document}/reject', 'reject')->name('reject');
    });

    Route::resource('revision', App\Http\Controllers\RevisionController::class);

    Route::resource('procedure', App\Http\Controllers\ProcedureController::class);
    Route::prefix('/procedur')->name('procedur.')->controller(App\Http\Controllers\ProcedureController::class)->group(function () {
        Route::patch('/{procedure}/left', 'left')->name('left');
        Route::patch('/{procedure}/right', 'right')->name('right');
        Route::patch('/{procedure}/up', 'up')->name('up');
        Route::patch('/{procedure}/down', 'down')->name('down');
    });

    Route::patch('/procedure-drill', [App\Http\Controllers\ProcedureController::class, 'drill'])->name('procedure.drill');

    Route::resource('content', App\Http\Controllers\ContentController::class);
});

Route::middleware(['auth:sanctum', 'role:superuser'])->prefix('superuser')->name('superuser.')->group(fn () => require __DIR__ . '/web/superuser.php');
Route::get('/test', [App\Http\Controllers\Test::class, 'index']);
