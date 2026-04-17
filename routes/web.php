<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('suppliers.index');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Suppliers
    Route::get('suppliers/detail/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::redirect('suppliers/{supplier}', '/suppliers/detail/{supplier}')->where('supplier', '[0-9]+');
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::get('suppliers/{supplier}/import', [SupplierController::class, 'importForm'])->name('suppliers.import');
    Route::post('suppliers/{supplier}/import/analyze', [SupplierController::class, 'importAnalyze'])->name('suppliers.import.analyze');
    Route::get('suppliers/{supplier}/import/review', [SupplierController::class, 'importReview'])->name('suppliers.import.review');
    Route::post('suppliers/{supplier}/import/execute', [SupplierController::class, 'importExecute'])->name('suppliers.import.execute');

    // Layups - standalone index + nested CRUD under Supplier
    Route::get('layups', [CltLayupController::class, 'index'])->name('layups.index');
    Route::post('suppliers/{supplier}/layups/{layup}/duplicate', [CltLayupController::class, 'duplicate'])->name('suppliers.layups.duplicate');
    Route::resource('suppliers.layups', CltLayupController::class)->except(['index']);

    // Layers - standalone index + nested CRUD under Supplier > Layup
    Route::get('layers', [CltLayerController::class, 'index'])->name('layers.index');
    Route::resource('suppliers.layups.layers', CltLayerController::class)->except(['index', 'show']);
});

require __DIR__.'/auth.php';
