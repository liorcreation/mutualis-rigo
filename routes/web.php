<?php

use App\Livewire\Admin\ContributionApproval;
use App\Livewire\Admin\ProjectReview;
use App\Livewire\CreateProject;
use App\Livewire\ProjectCatalog;
use App\Livewire\ProjectChat;
use App\Livewire\ProjectFaq;
use App\Livewire\PublicRegistry;
use App\Livewire\SecurityAudit;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

// Page d'accueil de présentation (Landing Page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Registre Public
Route::get('/registre', PublicRegistry::class)->name('registry');
Route::get('/projects', ProjectCatalog::class)->name('projects.catalog');

// Routes protégées
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/projects/create', CreateProject::class)->name('projects.create');
    Route::get('/projects/{project}/chat/{participant}', ProjectChat::class)->name('projects.chat');
    Route::middleware('role:responsable_rh,responsable_financier,chef_projet,top_management,admin_systeme')
        ->prefix('admin')
        ->group(function (): void {
            Route::get('/audit', SecurityAudit::class)->name('admin.audit');
            Route::get('/contributions', ContributionApproval::class)->name('admin.contributions');
            Route::get('/projects/review', ProjectReview::class)->name('admin.projects.review');
        });
});

Route::get('/projects/{project}', ProjectFaq::class)->name('projects.show');

require __DIR__.'/auth.php';
