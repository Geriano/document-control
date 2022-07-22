<?php

use Illuminate\Support\Facades\Route;

Route::post('/document', [App\Http\Controllers\DocumentController::class, 'paginate'])->name('document.paginate');
Route::post('/document/{document}/revisions', [App\Http\Controllers\RevisionController::class, 'paginate'])->name('revision.paginate');
Route::get('/revision/{revision}/procedures', [App\Http\Controllers\RevisionController::class, 'procedures'])->name('revision.procedures');

Route::prefix('superuser')->name('superuser.')->group(fn () => require __DIR__ . '/v1/superuser.php');