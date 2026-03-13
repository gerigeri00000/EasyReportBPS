<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSubmissionController;
use App\Http\Controllers\ReportPdfController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

// Activity Management Routes (protected - staff only)
Route::middleware(['auth'])->group(function () {
    Route::resource('activities', ActivityController::class);

    // Word Generation Route (single activity)
    Route::get('activities/{activity}/word/generate', [ReportPdfController::class, 'generate'])
        ->name('activities.word.generate');

    // Bulk Word Generation Route (multiple activities)
    Route::post('activities/bulk/word', [ReportPdfController::class, 'bulkGenerate'])
        ->name('activities.bulk.word');

    // Delete a submission within an activity
    Route::delete('activities/{activity}/submissions/{submission}', [ActivityController::class, 'destroySubmission'])
        ->name('activities.submissions.destroy');
});

// Public Submission Routes (no authentication required)
Route::prefix('submit')->name('public.')->group(function () {
    Route::get('/{uuid}', [PublicSubmissionController::class, 'showForm'])->name('form');
    Route::post('/{uuid}', [PublicSubmissionController::class, 'store'])->name('submit');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage link sukses dibuat!';
});

require __DIR__.'/auth.php';
