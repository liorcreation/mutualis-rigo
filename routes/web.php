<?php

use App\Http\Controllers\ContractDownloadController;
use App\Http\Controllers\ContractVerificationController;
use App\Livewire\Admin\ContributionApproval;
use App\Livewire\Admin\ProjectReview;
use App\Livewire\ContractCheckout;
use App\Livewire\CreateProject;
use App\Livewire\MaterialReservationCenter;
use App\Livewire\PaymentHistory;
use App\Livewire\ProjectCatalog;
use App\Livewire\ProjectChat;
use App\Livewire\ProjectFaq;
use App\Livewire\SecurityAudit;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

// Page d'accueil de présentation (Landing Page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/projects', ProjectCatalog::class)->name('projects.index');

// Routes protégées
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/projects/create', CreateProject::class)->name('projects.create');
    Route::get('/projects/{project}/chat/{participant}', ProjectChat::class)->name('projects.chat');
    Route::get('/contributions/{contribution}/contract', ContractCheckout::class)->name('contracts.checkout');
    Route::get('/contracts/{contract}/download', ContractDownloadController::class)->name('contracts.download');
    Route::get('/payments', PaymentHistory::class)->name('payments.history');
    Route::get('/materiel/reservations', MaterialReservationCenter::class)->name('material.reservations');
    Route::middleware('role:responsable_rh,responsable_financier,chef_projet,top_management,admin_systeme')
        ->prefix('admin')
        ->group(function (): void {
            Route::get('/audit', SecurityAudit::class)->name('admin.audit');
            Route::get('/contributions', ContributionApproval::class)->name('admin.contributions');
            Route::get('/projects/review', ProjectReview::class)->name('admin.projects.review');
        });
});

Route::get('/projects/{project}', ProjectFaq::class)->name('projects.show');

Route::get('/contracts/verify/{contract:contract_number}', ContractVerificationController::class)
    ->name('contracts.verify')
    ->middleware('throttle:20,1');

require __DIR__.'/auth.php';
