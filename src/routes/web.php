<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettlementController;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Invitation link (accessible without login to show the page)
Route::get('/invitations/{token}', [InvitationController::class, 'show'])
    ->name('invitations.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Colocations
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::get('/colocations/{colocation}/edit', [ColocationController::class, 'edit'])->name('colocations.edit');
    Route::put('/colocations/{colocation}', [ColocationController::class, 'update'])->name('colocations.update');
    Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'removeMember'])->name('colocations.removeMember');

    // Expenses
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Categories
    Route::post('/colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Invitations
    Route::post('/colocations/{colocation}/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');

    // Settlements
    Route::post('/colocations/{colocation}/settlements/{settlement}/pay', [SettlementController::class, 'markPaid'])->name('settlements.markPaid');
    Route::post('/colocations/{colocation}/settlements/recalculate', [SettlementController::class, 'recalculate'])->name('settlements.recalculate');

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/users/{user}/ban', [AdminController::class, 'ban'])->name('ban');
        Route::post('/users/{user}/unban', [AdminController::class, 'unban'])->name('unban');
    });
});

require __DIR__.'/auth.php';
