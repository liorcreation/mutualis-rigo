<?php

namespace App\Providers;

use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\Projet;
use App\Observers\ProjetObserver;
use App\Policies\ContributionPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(MutualizationContribution::class, ContributionPolicy::class);

        // On dit à Laravel d'associer le ProjetObserver au modèle Projet
        Projet::observe(ProjetObserver::class);
    }
}
